<div class="flex gap-x-2 items-center">

    {{-- Only show if AUTH is onwer of message --}}
    @if ($belongsToAuth)
        <span class="font-bold text-xs dark:text-black/90 dark:font-normal text-black">
            @lang('wirechat::chats.labels.you'):
        </span>
    @elseif(!$belongsToAuth && $group !== null)
        <span class="font-bold text-xs dark:text-white/80 dark:font-normal">
            {{ $lastMessage->sendable?->display_name }}:
        </span>
    @endif

    <p @class([
        'truncate text-sm dark:text-black gap-2 items-center mb-0 text-black',
        'font-semibold text-black' =>
            !$isReadByAuth && !$lastMessage?->ownedBy($this->auth),
        'font-normal text-gray-600' =>
            $isReadByAuth && !$lastMessage?->ownedBy($this->auth),
        'font-normal text-gray-600' =>
            $isReadByAuth && $lastMessage?->ownedBy($this->auth),
    ])>
        {{ $lastMessage->body != '' ? $lastMessage->body : ($lastMessage->isAttachment() ? '📎 ' . __('wirechat::chats.labels.attachment') : '') }}
    </p>

    <span class="font-medium px-0 text-xs shrink-0 text-black dark:text-gray-50 mt-2">
        @if ($lastMessage->created_at->diffInMinutes(now()) < 1)
            @lang('wirechat::chats.labels.now')
        @else
            {{ $lastMessage->created_at->shortAbsoluteDiffForHumans() }}
        @endif
    </span>


</div>