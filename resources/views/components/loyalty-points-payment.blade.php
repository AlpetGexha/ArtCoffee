<div
    x-data="{ usePoints: false, hasEnoughPoints: {{ $hasEnoughPoints ? 'true' : 'false' }} }"
    class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 mb-4"
>
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">Pay with Loyalty Points</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Your balance: {{ $pointsBalance }} ({{ $pointsValueFormatted }})
            </p>
        </div>
        <div class="flex items-center">
            <input
                type="checkbox"
                id="use-loyalty-points"
                name="use_loyalty_points"
                class="form-checkbox h-5 w-5 text-accent rounded border-zinc-300 focus:ring-accent"
                x-model="usePoints"
                @if(!$hasEnoughPoints) disabled @endif
                wire:model.live="usePoints"
            >
            <label
                for="use-loyalty-points"
                class="ml-2 text-sm font-medium @if(!$hasEnoughPoints) text-zinc-400 dark:text-zinc-600 cursor-not-allowed @else text-zinc-700 dark:text-zinc-300 cursor-pointer @endif"
            >
                Pay with points
            </label>
        </div>
    </div>

    <div class="bg-zinc-50 dark:bg-zinc-800 rounded p-3 text-sm" x-show="hasEnoughPoints" x-transition>
        @if($hasEnoughPoints)
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>You have enough points to cover this purchase ({{ $requiredPoints }} points)</span>
            </div>
        @endif
    </div>

    <div class="bg-zinc-50 dark:bg-zinc-800 rounded p-3 text-sm" x-show="!hasEnoughPoints" x-transition>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>You need {{ $requiredPoints }} points ({{ $requiredValueFormatted }}) to cover this purchase</span>
        </div>
    </div>
</div>
