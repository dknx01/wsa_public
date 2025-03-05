<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Captcha;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

use function Symfony\Component\String\u;

class CaptchaGenerator
{
    private Configuration $configuration;

    public function __construct(
        #[Autowire('%kernel.project_dir%/data/JetBrainsMono-ExtraBold.ttf')] private readonly string $captchaFontPath,
    ) {
        $this->configuration = new Configuration(u());
    }

    /**
     * @throws \Exception
     */
    public function generateCaptcha(): string
    {
        $this->build();

        return $this->inline();
    }

    public function getCaptchaCode(): string
    {
        return $this->configuration->captchaCode->toString();
    }

    /**
     * @throws \Exception
     */
    private function build(): void
    {
        $this->createImage();

        $text = new Text($this->configuration, $this->captchaFontPath);

        $text->generateText();
        $this->createBackground();
        $text->write();
        $this->addLines(new Line($this->configuration));
    }

    private function createImage(): void
    {
        $this->configuration->image = imagecreatetruecolor($this->configuration::WIDTH, $this->configuration::HEIGHT);
    }

    /**
     * @throws \Exception
     */
    private function createBackground(): void
    {
        $mix = Utils::hex2rgb($this->configuration::BACKGROUND_COLOR);

        $bgCode = imagecolorallocate($this->configuration->image, $mix[0], $mix[1], $mix[2]);

        imagefill($this->configuration->image, 0, 0, $bgCode);
    }

    /**
     * @throws \Exception
     */
    private function addLines(Line $line): void
    {
        $surface = $this->configuration::WIDTH * $this->configuration::HEIGHT;
        $effects = $this->randomFloat($surface / 3000, $surface / 2000);
        if (0 !== $this->configuration->maxLinesBehind) {
            for ($e = 0; $e < $effects; ++$e) {
                $line->draw();
            }
        }
    }

    private function inline(): string
    {
        return \sprintf('data:%s;base64,%s', 'image/jpeg', base64_encode($this->fetch()));
    }

    private function fetch(): string
    {
        ob_start();
        $this->gd2img();

        return ob_get_clean();
    }

    private function gd2img(): void
    {
        imagejpeg($this->configuration->image, null, 90);
    }

    private function randomFloat(float|int $min, float|int $max): float
    {
        // See https://www.php.net/manual/en/function.mt-rand.php#75793
        return $min + lcg_value() * abs($max - $min);
    }
}
