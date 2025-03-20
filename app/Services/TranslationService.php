<?php
namespace App\Services;

use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationService
{
    public function __construct(protected GoogleTranslate $translator)
    {
    }

    /**
     * @throws LargeTextException
     * @throws RateLimitException
     * @throws TranslationRequestException
     */
    public function translate(string $text, string $targetLanguage): string
    {
        $this->translator->setSource('en')->setTarget($targetLanguage);
        return $this->translator->translate($text);
    }
}
