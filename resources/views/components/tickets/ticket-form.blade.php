<form onsubmit="return false;"
    class="space-y-6 bg-white dark:bg-zinc-900 p-6 rounded-lg shadow border border-zinc-200 dark:border-zinc-800">
    <div>
        <label for="category" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Category</label>
        <select id="category" name="category"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option>Billing & Payments</option>
            <option>Technical Issue</option>
            <option>Feedback</option>
            <option>Other</option>
        </select>
    </div>

    <div>
        <label for="subject" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Subject</label>
        <div class="mt-1">
            <input type="text" name="subject" id="subject"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md"
                placeholder="Brief summary of issue">
        </div>
    </div>

    <div>
        <label for="message" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Message</label>
        <div class="mt-1">
            <textarea id="message" name="message" rows="4"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white rounded-md"
                placeholder="Describe your issue in detail..."></textarea>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Attachments</label>
        <div
            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-zinc-300 dark:border-zinc-700 border-dashed rounded-md">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-zinc-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"
                    aria-hidden="true">
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-zinc-600 dark:text-zinc-400">
                    <label for="file-upload"
                        class="relative cursor-pointer bg-white dark:bg-zinc-900 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>Upload a file</span>
                        <input id="file-upload" name="file-upload" type="file" class="sr-only">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-zinc-500">PNG, JPG, PDF up to 10MB</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Submit Ticket
        </button>
    </div>
</form>