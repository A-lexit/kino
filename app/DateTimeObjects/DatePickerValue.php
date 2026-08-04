<?php
namespace App\DateTimeObjects;

use Carbon\Carbon;

class DatePickerValue
{
    private ?string $date;

    public function __construct(?string $date)
    {
        $this->date = $date;
    }

    public function toForm(): ?string
    {
        return $this->date ? Carbon::parse($this->date)->format('d.m.Y') : null;
    }

    public function toDatabase(): ?string
    {
        if (!$this->date) {
            return null;
        }

        // Якщо дата вже в стандартному SQL форматі Y-m-d (наприклад, прийшла з бази)
        if (\preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date)) {
            return $this->date;
        }

        // Спроба розпарсити формат з крапками
        $date = \DateTime::createFromFormat('d.m.Y', $this->date);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }

        // Універсальний парсинг, якщо фронтенд надсилає дату в іншому вигляді
        try {
            return \Carbon\Carbon::parse($this->date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // або кинути помилку, якщо значення обов'язкове
        }
    }

    public function __toString(): string
    {
        return $this->toForm() ?? '';
    }
}
