<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Exception;

use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\System\ModelLocalizationStrings;
use Borlabs\Cookie\Model\AbstractModel;

class StillInUseModelDeletionException extends TranslatedException
{
    protected const LOCALIZATION_STRING_CLASS = GlobalLocalizationStrings::class;

    public AbstractModel $blockingModel;

    public AbstractModel $modelToDelete;

    private string $blockingModelLabel;

    private string $modelToDeleteLabel;

    /**
     * @param class-string<AbstractModel> $modelToDeleteLabel
     * @param class-string<AbstractModel> $blockingModelLabel
     */
    public function __construct(AbstractModel $modelToDelete, AbstractModel $blockingModel, string $modelToDeleteLabel, string $blockingModelLabel)
    {
        $this->modelToDelete = $modelToDelete;
        $this->blockingModel = $blockingModel;
        $this->modelToDeleteLabel = $modelToDeleteLabel;
        $this->blockingModelLabel = $blockingModelLabel;
        parent::__construct('modelStillInUse');
    }

    public function getTranslatedMessage(): string
    {
        $modelName = ModelLocalizationStrings::get()['models'][get_class($this->modelToDelete)] ?? get_class($this->modelToDelete);
        $blockingModelName = ModelLocalizationStrings::get()['models'][get_class($this->blockingModel)] ?? get_class($this->blockingModel);

        $this->context = [
            'modelLabel' => $this->modelToDeleteLabel,
            'modelName' => $modelName,
            'blockingModelLabel' => $this->blockingModelLabel,
            'blockingModelName' => $blockingModelName,
        ];

        return parent::getTranslatedMessage();
    }
}
