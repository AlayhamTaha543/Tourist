<div class="mb-3 text-center">
    <button class="btn btn-primary d-grid w-100" type="button" wire:click="submit" wire:loading.attr="disabled">
        <span wire:loading.remove>Resend Code</span>
        <div class="text-center" wire:loading>
            <span class="spinner-border spinner-border-sm text-white" role="status">
                <span class="visually-hidden">Loading...</span>
            </span>
        </div>
    </button>
</div>
