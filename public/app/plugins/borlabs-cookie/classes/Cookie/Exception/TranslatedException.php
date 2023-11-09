<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Exception;

use Borlabs\Cookie\Localization\LocalizationInterface;
use Borlabs\Cookie\Support\Formatter;

class TranslatedException extends GenericException
{
    /**
     * @var class-string<LocalizationInterface>
     */
    protected const LOCALIZATION_STRING_CLASS = \Borlabs\Cookie\Localization\GlobalLocalizationStrings::class;

    protected ?array $context;

    /**
     * @var null|class-string<LocalizationInterface>
     */
    protected ?string $localizationStringClass;

    protected string $translationKey;

    /**
     * @param null|class-string<LocalizationInterface> $localizationStringClass
     */
    public function __construct(string $translationKey, ?array $context = null, ?string $localizationStringClass = null)
    {
        $this->translationKey = $translationKey;
        $this->localizationStringClass = $localizationStringClass;
        $this->context = $context;
        parent::__construct();
    }

    /**
     * Gets the Exception message.
     *
     * @return string the Exception message as a string
     */
    public function getTranslatedMessage(): string
    {
        if ($this->localizationStringClass !== null) {
            $localizationStrings = call_user_func([$this->localizationStringClass, 'get']);

            if (isset($localizationStrings['alert'][$this->translationKey])) {
                return $this->getTranslationWithContext($localizationStrings['alert'][$this->translationKey]);
            }
        }

        if (isset(call_user_func([static::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey])) {
            return $this->getTranslationWithContext(call_user_func([static::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey]);
        }

        if (isset(call_user_func([self::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey])) {
            return $this->getTranslationWithContext(call_user_func([self::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey]);
        }

        return $this->translationKey;
    }

    protected function getTranslationWithContext(string $translation): string
    {
        if ($this->context !== null) {
            return Formatter::interpolate($translation, $this->context);
        }

        return $translation;
    }
}
