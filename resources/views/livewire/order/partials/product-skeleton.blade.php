<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        @for ($i = 0; $i < 6; $i++)
            <div class="bg-white rounded-lg shadow overflow-hidden animate-pulse">
                <!-- Image Placeholder -->
                <div class="bg-gray-200 p-3 flex justify-center items-center h-36 sm:h-48"></div>

                <div class="p-3 sm:p-4">
                    <!-- Title & Price Placeholder -->
                    <div class="flex justify-between items-start">
                        <div class="w-2/3">
                            <div class="h-5 bg-gray-200 rounded w-full mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-full mb-1"></div>
                            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                        </div>
                        <div class="w-1/4">
                            <div class="h-5 bg-gray-200 rounded w-full"></div>
                        </div>
                    </div>

                    <!-- Buttons Placeholder -->
                    <div class="mt-3 sm:mt-4 flex gap-2">
                        <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Empty State -->
    <div class="hidden">
        <div class="col-span-full flex flex-col items-center justify-center py-8 sm:py-12 bg-white rounded-lg shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 sm:h-12 sm:w-12 text-amber-300 mb-3"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500 text-center text-sm sm:text-base">Loading products...</p>
        </div>
    </div>

</div>
