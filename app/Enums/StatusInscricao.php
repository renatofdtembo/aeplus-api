<?php

namespace App\Enums;

enum StatusInscricao: string
{
    case PENDENTE = 'PENDENTE';
    case ATIVO = 'ATIVO';
    case CONCLUIDO = 'CONCLUIDO';
    case CANCELADO = 'CANCELADO';
    case TRANCADO = 'TRANCADO';

    public function descricao(): string
    {
        return match($this) {
            self::PENDENTE => 'Inscrição Pendente',
            self::ATIVO => 'Inscrição Ativa',
            self::CONCLUIDO => 'Inscrição Concluída',
            self::CANCELADO => 'Inscrição Cancelada',
            self::TRANCADO => 'Inscrição Trancada',
        };
    }
}
