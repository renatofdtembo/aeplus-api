<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Salva arquivos enviados e retorna os caminhos
     * @param array $files Array de UploadedFile ou array de arrays de UploadedFile
     * @param string $directory Diretório de armazenamento (ex: 'documentos')
     * @param string $disk Disco de armazenamento (default: 'public')
     * @return array
     */
    public function saveFiles(array $files, string $directory = 'documentos', string $disk = 'public'): array
    {
        $savedPaths = [];

        foreach ($files as $field => $file) {
            if (is_array($file)) {
                // Múltiplos arquivos para o mesmo campo
                $savedPaths[$field] = $this->saveMultipleFiles($file, $directory, $disk);
            } elseif ($file instanceof UploadedFile) {
                // Único arquivo
                $savedPaths[$field] = $this->saveSingleFile($file, $directory, $disk);
            }
        }

        return $savedPaths;
    }

    /**
     * Salva um único arquivo
     */
    protected function saveSingleFile(UploadedFile $file, string $directory, string $disk): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;

        $path = $file->storeAs(
            $directory,
            $filename,
            ['disk' => $disk]
        );

        return $this->getRelativePath($path, $disk);
    }

    /**
     * Salva múltiplos arquivos
     */
    protected function saveMultipleFiles(array $files, string $directory, string $disk): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->saveSingleFile($file, $directory, $disk);
            }
        }

        return $paths;
    }

    /**
     * Move/transfere um arquivo para novo local
     * 
     * @param string $currentPath Caminho atual relativo ao disco
     * @param string $newPath Novo caminho relativo ao disco
     * @param string $disk Disco de armazenamento
     * @return string Novo caminho
     * @throws \RuntimeException
     */
    public function moveFile(string $currentPath, string $newPath, string $disk = 'public'): string
    {
        $storage = Storage::disk($disk);

        if (!$storage->exists($currentPath)) {
            throw new \RuntimeException("Arquivo não encontrado: {$currentPath}");
        }

        // Cria diretório se não existir
        $newDirectory = dirname($newPath);
        if (!$storage->exists($newDirectory)) {
            $storage->makeDirectory($newDirectory);
        }

        if ($storage->move($currentPath, $newPath)) {
            return $newPath;
        }

        throw new \RuntimeException("Falha ao mover arquivo de {$currentPath} para {$newPath}");
    }

    /**
     * Converte path completo para relativo
     */
    public function getRelativePath(string $fullPath): string
    {
        return str_replace(storage_path('app/public/'), '', $fullPath);
    }

    /**
     * Converte path completo para relativo
     */
    public function saveWithCustomName(string $file, string $directory, string $customFilename): string {
        return '';
    }

    /**
     * Cria um diretório no disco especificado
     *
     * @param string $directory Caminho relativo da pasta (ex: 'documentos/novos')
     * @param string $disk Disco de armazenamento (default: 'public')
     * @return bool
     */
    public function createDirectory(string $directory, string $disk = 'public'): bool
    {
        $storage = Storage::disk($disk);

        if ($storage->exists($directory)) {
            return true; // Já existe
        }

        return $storage->makeDirectory($directory);
    }

    /**
     * Deleta uma pasta com todos os arquivos e subpastas contidos nela
     *
     * @param string $directory Caminho relativo da pasta (ex: 'documentos/velhos')
     * @param string $disk Disco de armazenamento (default: 'public')
     * @return bool
     */
    public function deleteDirectory(string $directory, string $disk = 'public'): bool
    {
        $storage = Storage::disk($disk);

        if (!$storage->exists($directory)) {
            return true; // Já não existe
        }

        return $storage->deleteDirectory($directory);
    }

    /**
     * Remove arquivos do storage
     * 
     * @param array|string $paths Caminho(s) do(s) arquivo(s)
     * @param string $disk Disco de armazenamento
     * @return bool
     */
    public function deleteFiles($paths, string $disk = 'public'): bool
    {
        if (empty($paths)) {
            return true;
        }

        $paths = is_array($paths) ? $paths : [$paths];

        return Storage::disk($disk)->delete($paths);
    }
    /**
     * Renomeia um arquivo ou pasta
     *
     * @param string $oldPath Caminho antigo (relativo ao disco)
     * @param string $newName Novo nome do arquivo ou pasta
     * @param string $disk Disco de armazenamento (default: 'public')
     * @return string Novo caminho completo após renomear
     * @throws \RuntimeException
     */
    public function rename(string $oldPath, string $newName, string $disk = 'public'): string
    {
        $storage = Storage::disk($disk);

        if (!$storage->exists($oldPath)) {
            throw new \RuntimeException("Arquivo ou diretório não encontrado: {$oldPath}");
        }

        $directory = dirname($oldPath);
        $newPath = $directory === '.' ? $newName : "{$directory}/{$newName}";

        $success = $storage->move($oldPath, $newPath);

        if (!$success) {
            throw new \RuntimeException("Falha ao renomear {$oldPath} para {$newPath}");
        }

        return $newPath;
    }
}
