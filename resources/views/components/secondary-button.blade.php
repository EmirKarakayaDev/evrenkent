<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 bg-white border border-slate-300 rounded-lg font-medium text-sm text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 disabled:opacity-40 transition-colors']) }}>
    {{ $slot }}
</button>
