<?php

namespace App\Enums;

enum TipoAtividade: string
{
    case VIDEO = 'VIDEO';
    case FORUM = 'FORUM';
    case TEXTO = 'TEXTO';
    case QUIZ = 'QUIZ';
    case ARTIGO = 'ARTIGO';
    case ROTULO = 'ROTULO';
    case CERTIFICADO = 'CERTIFICADO';
    case PAGINA = 'PAGINA';
    case TAREFA = 'TAREFA';

    public function label(): string
    {
        return match($this) {
            self::VIDEO => 'Vídeo Aula',
            self::FORUM => 'Fórum de Discussão',
            self::TEXTO => 'Material de Leitura',
            self::QUIZ => 'Questionário',
            self::ARTIGO => 'Artigo',
            self::ROTULO => 'Rótulo',
            self::CERTIFICADO => 'Certificado',
            self::PAGINA => 'Páginas do Site',
            self::TAREFA => 'Tarefa para Entregar',
        };
    }
}
