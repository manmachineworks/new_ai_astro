<form action="#" method="POST" class="d-flex align-items-center gap-2">
    @csrf
    <button type="button" class="btn btn-link text-secondary p-1">
        <i class="bi bi-paperclip fs-4"></i>
    </button>
    <input type="text" name="message" class="form-control rounded-pill bg-light border-0 px-4 py-2"
        placeholder="Type your message..." required>
    <button type="submit"
        class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-2 shadow-sm"
        style="width: 40px; height: 40px;">
        <i class="bi bi-send-fill fs-5" style="transform: rotate(45deg); margin-left: -2px; margin-top: -2px;"></i>
    </button>
</form>