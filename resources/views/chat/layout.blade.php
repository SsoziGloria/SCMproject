<!DOCTYPE html>

<head>
    @wirechatStyles <!-- injects WireChat’s CSS -->
    <!-- Your custom <style> overrides can go here -->
    <style>
        :root {
            --wc-brand-primary: '#c27f20';

            --wc-light-primary: #fff;
            /* white */
            --wc-light-secondary: oklch(0.985 0.002 247.839);
            /* --color-gray-100 */
            --wc-light-accent: oklch(0.985 0.002 247.839);
            /* --color-gray-50 */
            --wc-light-border: oklch(0.928 0.006 264.531);
            /* --color-gray-200 */

            --wc-dark-primary: (179, 41, 121);
            /* --color-zinc-900 */
            --wc-dark-secondary: oklch(0.274 0.006 286.033);
            /* --color-zinc-800 */
            --wc-dark-accent: oklch(0.37 0.013 285.805);
            /* --color-zinc-700 */
            --wc-dark-border: oklch(0.37 0.013 285.805);
            /* --color-zinc-700 */
        }
    </style>
</head>

<body>
    <div class="h-screen">{{ $slot }}</div> <!-- Chat content -->
    @wirechatAssets <!-- injects JS/Livewire scripts -->
</body>

</html>