<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Infolists\Components\KeyValueEntry;
use Illuminate\Support\Collection;

class WizKeyValueEntry extends KeyValueEntry
{
    /**
     * Cờ kiểm soát việc hiển thị header (thead).
     */
    protected bool $showHeader = false;

    /**
     * Thiết lập hiển thị header.
     */
    public function showHeader(bool $visible = true): static
    {
        $this->showHeader = $visible;

        return $this;
    }

    /**
     * Render HTML của component.
     */
    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class(['fi-in-key-value']);

        ob_start(); ?>

        <table <?= $attributes->toHtml() ?>>
            <?php if ($this->showHeader) { ?>
                <thead>
                <tr>
                    <th scope="col">
                        <?= e($this->getKeyLabel()) ?>
                    </th>

                    <th scope="col">
                        <?= e($this->getValueLabel()) ?>
                    </th>
                </tr>
                </thead>
            <?php } ?>

            <tbody>
            <?php foreach (($state ?? []) as $key => $value) { ?>
                <tr>
                    <td><?= e($key) ?></td>
                    <td><?= e($value) ?></td>
                </tr>
            <?php } ?>

            <?php if (empty($state)) { ?>
                <tr>
                    <td colspan="2" class="fi-in-placeholder">
                        <?= e($this->getPlaceholder()) ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
