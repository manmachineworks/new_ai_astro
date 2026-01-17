const functions = require("firebase-functions");
const admin = require("firebase-admin");

admin.initializeApp();

/**
 * Triggers when a new message is created in a chat.
 * Sends FCM notification to the recipient.
 */
exports.sendChatMessageNotification = functions.firestore
    .document("chats/{chatId}/messages/{messageId}")
    .onCreate(async (snap, context) => {
        const message = snap.data();
        const { chatId } = context.params;

        // 1. Get Chat Metadata to find Recipient
        const chatDoc = await admin.firestore().collection("chats").doc(chatId).get();
        if (!chatDoc.exists) {
            console.log(`Chat ${chatId} not found.`);
            return null;
        }

        const chatData = chatDoc.data();
        // Assuming chatData.participants is an array of UIDs or map
        // And message.senderId is the sender.
        const senderId = message.senderId;

        // Identify Recipient (Simple logic for 1-on-1)
        let recipientId = null;
        if (chatData.participants) {
            if (Array.isArray(chatData.participants)) {
                recipientId = chatData.participants.find(uid => uid !== senderId);
            } else {
                // Map?
                recipientId = Object.keys(chatData.participants).find(uid => uid !== senderId);
            }
        }

        if (!recipientId) {
            console.log("No recipient found.");
            return null;
        }

        // 2. Check Preferences (Optional: If mirrored)
        // const prefs = await admin.firestore().doc(`users/${recipientId}/settings/notifications`).get();
        // if (prefs.exists && prefs.data().mute_chat) return;

        // 3. Fetch Tokens from Firestore (Approach A)
        const tokensSnap = await admin.firestore()
            .collection("device_tokens")
            .doc(recipientId)
            .collection("tokens")
            .where("is_enabled", "==", true)
            .get();

        if (tokensSnap.empty) {
            console.log(`No active tokens for user ${recipientId}`);
            return null;
        }

        const tokens = tokensSnap.docs.map(doc => doc.id); // Doc ID is the token

        // 4. Build Payload
        // Safe Masking: We don't have user names here easily unless in Chat Doc.
        // Use generic or what's in chatData.
        const senderLabel = chatData.participantNames ? chatData.participantNames[senderId] : "New Message";

        const payload = {
            notification: {
                title: senderLabel,
                body: truncate(message.text || "Photo/Media", 80),
            },
            data: {
                type: "chat_message",
                chat_id: chatId,
                click_action: "FLUTTER_NOTIFICATION_CLICK",
                deeplink: `app://chat/${chatId}`,
            },
            android: {
                priority: "high",
                notification: {
                    channel_id: "chat_messages",
                    tag: `chat_${chatId}`, // Collapse key
                }
            },
            apns: {
                payload: {
                    aps: {
                        "thread-id": `chat_${chatId}`
                    }
                }
            }
        };

        // 5. Send Multicast
        const response = await admin.messaging().sendToDevice(tokens, payload);

        // 6. Cleanup Invalid Tokens
        const tokensToRemove = [];
        response.results.forEach((result, index) => {
            const error = result.error;
            if (error) {
                console.error("Failure sending notification to", tokens[index], error);
                if (error.code === 'messaging/invalid-registration-token' ||
                    error.code === 'messaging/registration-token-not-registered') {
                    tokensToRemove.push(
                        admin.firestore()
                            .collection("device_tokens")
                            .doc(recipientId)
                            .collection("tokens")
                            .doc(tokens[index])
                            .delete()
                    );
                }
            }
        });

        await Promise.all(tokensToRemove);
        return null;
    });

function truncate(str, n) {
    return (str.length > n) ? str.substr(0, n - 1) + '...' : str;
}
