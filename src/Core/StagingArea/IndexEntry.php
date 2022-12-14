<?php

declare(strict_types=1);
/**
 * happy coding!!!
 */
namespace Kit\Core\StagingArea;

use Kit\Core\FileSystem\FileMode;
use Kit\Core\FileSystem\FileType;

class IndexEntry
{
    /**
     * 上次修改时间.
     */
    protected ?int $mtime = null;

    /**
     * 创建时间.
     */
    protected ?int $ctime = null;

    protected ?string $fileName = null;

    protected ?string $dirHash = null;

    protected ?string $fileHash = null;

    protected ?string $path = null;

    protected ?FileMode $fileMode = null;

    protected ?FileType $fileType = null;

    /**
     * @var IndexEntry[]
     */
    protected array $entries = [];

    public function __toString(): string
    {
        $hash = $this->getFileHash() ?: ($this->getDirHash() ?: '');
        return sprintf(
            '%d %s %s %s',
            $this->getFileMode()->getMode(),
            $hash,
            $this->getFileName(),
            $this->getPath()
        );
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function addEntries(IndexEntry $entries): self
    {
        $this->entries[] = $entries;
        return $this;
    }

    public function getMtime(): ?int
    {
        return $this->mtime;
    }

    public function setMtime(?int $mtime): self
    {
        $this->mtime = $mtime;
        return $this;
    }

    public function getCtime(): ?int
    {
        return $this->ctime;
    }

    public function setCtime(?int $ctime): self
    {
        $this->ctime = $ctime;
        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getDirHash(): ?string
    {
        return $this->dirHash;
    }

    public function setDirHash(?string $dirHash): self
    {
        $this->dirHash = $dirHash;
        return $this;
    }

    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    public function setFileHash(?string $fileHash): self
    {
        $this->fileHash = $fileHash;
        return $this;
    }

    public function getFileMode(): ?FileMode
    {
        return $this->fileMode;
    }

    public function setFileMode(?FileMode $fileMode): self
    {
        $this->fileMode = $fileMode;
        return $this;
    }

    public function getFileType(): ?FileType
    {
        return $this->fileType;
    }

    public function setFileType(?FileType $fileType): self
    {
        $this->fileType = $fileType;
        return $this;
    }
}
