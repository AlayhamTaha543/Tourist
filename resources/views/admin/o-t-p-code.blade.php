<div>
    <form class="mb-3" wire:submit.prevent="submit">
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <label for="code" class="form-label">OTP Code</label>
            <input
                type="text"
                class="form-control"
                placeholder="Enter the 4-digit code"
                wire:model="code"
                maxlength="4"
            />
            @error('code')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>Verify</span>
                <div class="text-center" wire:loading>
                    <span class="spinner-border spinner-border-sm text-white" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </div>
            </button>
        </div>
    </form>
</div>