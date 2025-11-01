<?php

namespace App\Enums;

enum TipoComentario: string
{
    case COMENTARIO = 'COMENTARIO';
    case RESPOSTA = 'RESPOSTA';

    public function label(): string
    {
        return match($this) {
            self::COMENTARIO => 'Comentário Primário',
            self::RESPOSTA => 'Resposta de um Comentário',
        };
    }
}

