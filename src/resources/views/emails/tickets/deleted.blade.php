@component('mail::message')
    # Ticket removido do sistema

    Olá, **{{ $ticket->client->name }}**!

    Informamos que o seu ticket foi removido do sistema.

    @component('mail::panel')
        **Ticket:** #{{ $ticket->id }} — {{ $ticket->title }}
        **Removido por:** {{ $deletedBy->name }}
    @endcomponent

    Se acredita que isso foi um engano, entre em contato com nossa equipe.

    Atenciosamente,
    **{{ config('app.name') }}**
@endcomponent
