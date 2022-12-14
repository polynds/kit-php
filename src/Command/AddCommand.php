<?php

declare(strict_types=1);
/**
 * happy coding!!!
 */
namespace Kit\Command;

use Kit\ApplicationContext;
use Kit\Core\FileSystem\FileMode;
use Kit\Core\FileSystem\FileNode;
use Kit\Core\FileSystem\FileType;
use Kit\Core\ObjectDatabase\Objects\Blob;
use Kit\Core\StagingArea\IndexEntry;
use Kit\Exception\ParameterErrorException;

class AddCommand extends AbstractCommand
{
    protected function handle(array $parameter = [])
    {
        $file = $parameter[0] ?? '';
        $ignore = ApplicationContext::getApplication()->getKitIgnore()->reload();
        $ignoreDirs = $ignore->getIgnoreDirs();
        $ignoreFiles = $ignore->getIgnoreFiles();
        $ignoreFiles = array_merge($ignoreDirs, $ignoreFiles);
        $files = ApplicationContext::getApplication()->getWorkSpace()->readFiles($file, $ignoreFiles)->scan();
        $indexs = $this->stageFiles($files);
        $stagingArea = ApplicationContext::getApplication()->getRepository()->getStagingArea();
        foreach ($indexs as $index) {
            $stagingArea->addIndex($index);
        }
        $stagingArea->store();
    }

    /**
     * @param FileNode[] $files
     * @return IndexEntry[]
     */
    protected function stageFiles(array $files): array
    {
        // 收集文件和目录信息放入暂存区，再数据库中保存为一个文件
        $indexs = [];
        $objectDatabase = ApplicationContext::getApplication()->getRepository()->getObjectDatabase();
        foreach ($files as $file) {
            if ($file->getType()->isDirectory()) {
//                var_dump($file->getPath(),$file->getRelativelyPath(ApplicationContext::getApplication()->getBasePath()));
//                $indexs[] = (new IndexEntry())
//                    ->setPath($file->getRelativelyPath(ApplicationContext::getApplication()->getBasePath()))
//                    ->setFileMode(FileMode::init(FileMode::DIRECTORY))
//                    ->setFileType(FileType::init(FileType::TYPE_DIR))
//                    ->setFileHash((new Hash($file->getName()))->getHashString())
//                    ->setFileName($file->getName())
//                    ->setMtime($file->getMtime())
//                    ->setCtime($file->getCtime());
                $indexs = array_merge($indexs, $this->stageFiles($file->getFiles()));
            } else {
                $blob = new Blob(file_get_contents($file->getPath()));
                $objectDatabase->store($blob);
                $indexs[] = (new IndexEntry())
                    ->setPath($file->getRelativelyPath(ApplicationContext::getApplication()->getBasePath()))
                    ->setFileMode(FileMode::init(FileMode::NORMAL_FILES))
                    ->setFileType(FileType::init(FileType::TYPE_FILE))
                    ->setFileHash($blob->getHashString())
                    ->setFileName($file->getName())
                    ->setMtime($file->getMtime())
                    ->setCtime($file->getCtime());
            }
        }
        return $indexs;
    }

    protected function validated(array $parameter = []): array
    {
        $file = $parameter[0] ?? '';
        if (! $file) {
            throw new ParameterErrorException();
        }

        $path = ApplicationContext::getApplication()->getBasePath() . (str_starts_with($file, DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR) . $file;
        if (! is_dir($path) && ! file_exists($path)) {
            throw new ParameterErrorException('The file or directory does not exist.');
        }

        return $parameter;
    }
}
