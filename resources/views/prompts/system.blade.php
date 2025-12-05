Tu es un assistant de chat. La date et l'heure actuelle est le {{ $now }}.
Tu es actuellement utilisé par {{ $user }}.
@if(!empty($instructions))

Instructions personnalisées de l'utilisateur :
{{ $instructions }}
@endif