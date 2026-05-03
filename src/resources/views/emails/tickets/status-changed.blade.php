@component('mail::message')
    # Atualização do seu ticket

    Olá, **{{ $ticket->client->name }}**!

    O status do seu ticket foi atualizado.

    @component('mail::panel')
        **Ticket:** #{{ $ticket->id }} — {{ $ticket->title }}
        **Status anterior:** {{ $oldStatus->value }}
        **Novo status:** {{ $newStatus->value }}
    @endcomponent

    Caso tenha dúvidas, entre em contato com nossa equipe de suporte.

    Atenciosamente,
    **{{ config('app.name') }}**
@endcomponent
