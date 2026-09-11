<?php

namespace App\Filament\Concerns;

use App\Support\TranslatableFormData;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithTranslatableFilamentRecord
{
    protected function translatableFormModel(): Model
    {
        return new (static::getResource()::getModel());
    }

    protected function hasTranslatableForm(): bool
    {
        $model = $this->translatableFormModel();

        return method_exists($model, 'getTranslatableAttributes')
            && $model->getTranslatableAttributes() !== [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! $this instanceof EditRecord) {
            return $data;
        }

        if (! $this->hasTranslatableForm()) {
            return parent::mutateFormDataBeforeFill($data);
        }

        $data = parent::mutateFormDataBeforeFill($data);

        $record = $this->getRecord();
        $attributes = $record->getTranslatableAttributes();

        foreach ($attributes as $attr) {
            unset($data[$attr]);
        }

        return array_merge($data, TranslatableFormData::expandForRecord($record, $attributes));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! $this instanceof CreateRecord) {
            return $data;
        }

        $data = parent::mutateFormDataBeforeCreate($data);

        if (! $this->hasTranslatableForm()) {
            return $data;
        }

        return TranslatableFormData::collapse($data, $this->translatableFormModel()->getTranslatableAttributes());
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! $this instanceof EditRecord) {
            return $data;
        }

        $data = parent::mutateFormDataBeforeSave($data);

        if (! $this->hasTranslatableForm()) {
            return $data;
        }

        return TranslatableFormData::collapse($data, $this->translatableFormModel()->getTranslatableAttributes());
    }
}
