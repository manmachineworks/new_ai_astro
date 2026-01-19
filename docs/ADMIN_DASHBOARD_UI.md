# Admin Dashboard UI Plan

## Navigation Structure
- Access Control: Admin Users, Roles and Permissions
- Management: Users, Astrologers
- Operations: Calls, Chats, AI Chats, Appointments
- Moderation: Chat Moderation, Reviews, Complaints, Support Tickets, Banned Words
- Finance Ops: Payments, Wallet Ledger, Earnings and Payouts, Refunds, Commission Settings, Exports
- Reporting: Overview, Revenue, Recharges, Top Astrologers
- System: Webhooks, Audit Logs, AI Settings, Pricing Rules

## Pages and Components
- Admin Dashboard: KPI cards, charts, quick actions
- Users: list, filters, profile view with wallet ledger and activity tabs
- Admin Users: create/edit/disable, last login, force logout
- Astrologers: list, profile tabs (overview, verification, services, schedule, performance, reviews)
- Calls: logs, filters, detail view, webhook events
- Chats: session list, detail view, reports
- Chat Moderation: conversation list and viewer, mute tools, analytics
- AI Chats: session list, detail view
- Reviews: moderation list with hide/delete actions
- Disputes/Complaints: list, review, refund actions
- Support Tickets: list, detail, reply/close/resolve
- Wallets/Payments: exports, adjustments, ledger view
- AI Settings: pricing, provider config, rate limits, enable/disable
- Webhooks and Audit Logs: filters and detail views

## Controller to View and Route Mapping
- Admin Dashboard: `Admin/DashboardController@index` -> `resources/views/admin/dashboard.blade.php` -> `admin.dashboard`
- Admin Users: `Admin/AdminUserManagementController` -> `resources/views/admin/admin_users/*.blade.php` -> `admin.admin-users.*`
- Roles: `Admin/AdminRoleController` -> `resources/views/admin/roles/*.blade.php` -> `admin.roles.*`
- Users: `Admin/AdminUserController` -> `resources/views/admin/users/*.blade.php` -> `admin.users.*`
- Astrologers: `Admin/AdminAstrologerController` -> `resources/views/admin/astrologers/*.blade.php` -> `admin.astrologers.*`
- Calls: `Admin/CallController` -> `resources/views/admin/calls/*.blade.php` -> `admin.calls.*`
- Chats: `Admin/AdminChatController` -> `resources/views/admin/chats/*.blade.php` -> `admin.chats.*`
- Chat Moderation: `Admin/ChatModerationController` -> `resources/views/admin/moderation/chats/index.blade.php` -> `admin.moderation.chats.*`
- Banned Words: `Admin/ChatBannedWordController` -> `resources/views/admin/moderation/banned_words/index.blade.php` -> `admin.moderation.banned_words.*`
- AI Chats: `Admin/AdminAiChatController` -> `resources/views/admin/ai/chats/*.blade.php` -> `admin.ai_chats.*`
- AI Settings: `Admin/AiSettingsController` -> `resources/views/admin/ai/settings.blade.php` -> `admin.ai.settings`
- Reviews: `Admin/AdminReviewController` -> `resources/views/admin/reviews/index.blade.php` -> `admin.reviews.*`
- Disputes: `Admin/DisputeController` -> `resources/views/admin/disputes/*.blade.php` -> `admin.disputes.*`
- Support: `Admin/SupportController` -> `resources/views/admin/support/*.blade.php` -> `admin.support.*`
- Finance: `Admin/Finance/*` -> `resources/views/admin/finance/**/*` -> `admin.finance.*`
- Reports: `Admin/ReportingController` -> `resources/views/admin/reports/*` -> `admin.reports.*`
- Webhooks: `Admin/AdminWebhookController` -> `resources/views/admin/webhooks/*.blade.php` -> `admin.system.webhooks.*`
- Audit Logs: `Admin/AdminAuditLogController` -> `resources/views/admin/audit_logs/*.blade.php` -> `admin.system.audit_logs.*`

## Permissions Mapping
- manage_roles: Admin users, roles and permissions
- view_users, manage_users, export_users: user list/profile actions
- view_astrologers, manage_astrologers, verify_astrologers, toggle_astrologer_visibility: astrologer management
- view_calls, manage_calls: call logs and dispute checks
- view_chats, manage_chats: chat sessions and moderation tools
- manage_reviews: reviews, disputes, support tickets
- view_ai_chats, manage_ai_settings: AI chat logs and settings
- view_finance, manage_payments, wallet_adjustments, issue_refunds, manage_payouts, manage_commissions, export_finance: finance ops
- view_webhooks, retry_webhooks: webhook debugging
- view_audit_logs: admin audit log viewer

## Export Implementation Plan
- CSV exports for list views using `response()->stream()` or `CsvExportService` for large datasets.
- Use query filters from request to scope exports (date ranges, status, user, astrologer).
- For large exports, queue a background job, store the file in storage, and provide a download link.
- Add `export=csv` query parameter for list endpoints to keep UI simple and consistent.
