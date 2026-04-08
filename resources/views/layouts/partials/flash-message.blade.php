@if (session('success'))
    <div class="mb-4 rounded-soft border border-[#d9d6c8] bg-[#f2f7ef] px-4 py-3 text-sm text-[#35533b]">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-soft border border-[#dfc4c4] bg-[#fbefef] px-4 py-3 text-sm text-dangerWarm">
        {{ session('error') }}
    </div>
@endif
