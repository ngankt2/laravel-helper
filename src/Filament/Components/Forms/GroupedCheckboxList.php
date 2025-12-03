<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Closure;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class GroupedCheckboxList extends CheckboxList
{
    protected string | Closure | null $groupByRelation = null;
    
    protected string | Closure | null $groupLabelAttribute = null;
    
    protected string | Closure | null $groupIconAttribute = null;
    
    protected Closure | null $groupLabelUsing = null;
    
    protected string $view = 'filament.forms.grouped-checkbox-list';

    /**
     * Specify the relationship or attribute to group options by.
     */
    public function groupBy(string | Closure $relation): static
    {
        $this->groupByRelation = $relation;
        
        return $this;
    }

    /**
     * Specify the attribute to use for group labels.
     */
    public function groupLabelAttribute(string | Closure $attribute): static
    {
        $this->groupLabelAttribute = $attribute;
        
        return $this;
    }

    /**
     * Specify the attribute to use for group icons.
     */
    public function groupIconAttribute(string | Closure $attribute): static
    {
        $this->groupIconAttribute = $attribute;
        
        return $this;
    }

    /**
     * Customize how group labels are displayed.
     */
    public function groupLabelUsing(Closure $callback): static
    {
        $this->groupLabelUsing = $callback;
        
        return $this;
    }

    /**
     * Get the grouped options.
     */
    public function getGroupedOptions(): array
    {
        $options = $this->getOptions();
        
        if (empty($options) || !$this->groupByRelation) {
            return ['_ungrouped' => [
                'label' => null,
                'icon' => null,
                'options' => $options,
            ]];
        }

        $groupByRelation = $this->evaluate($this->groupByRelation);
        $groupLabelAttr = $this->evaluate($this->groupLabelAttribute) ?? 'name';
        $groupIconAttr = $this->evaluate($this->groupIconAttribute);
        
        // Get the model class from relationship
        $relationship = $this->getRelationship();
        
        if (!$relationship) {
            return ['_ungrouped' => [
                'label' => null,
                'icon' => null,
                'options' => $options,
            ]];
        }
        
        // Get all items with their groups
        $relatedModel = $relationship->getRelated();
        $items = $relatedModel::query()
            ->with($groupByRelation)
            ->whereIn('id', array_keys($options))
            ->get();
        
        // Group items by their group
        $grouped = [];
        
        foreach ($items as $item) {
            $group = $item->{$groupByRelation};
            
            if (!$group) {
                $groupKey = '_ungrouped';
                $groupLabel = null;
                $groupIcon = null;
            } else {
                $groupKey = 'group_' . $group->id;
                
                // Get group label
                if ($this->groupLabelUsing) {
                    $groupLabel = $this->evaluate($this->groupLabelUsing, ['group' => $group]);
                } else {
                    $groupLabel = $group->{$groupLabelAttr};
                    
                    // Handle translatable attributes
                    if (is_array($groupLabel)) {
                        $groupLabel = $groupLabel[app()->getLocale()] ?? $groupLabel['en'] ?? reset($groupLabel);
                    }
                }
                
                // Get group icon
                $groupIcon = $groupIconAttr ? $group->{$groupIconAttr} : null;
            }
            
            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'label' => $groupLabel,
                    'icon' => $groupIcon,
                    'options' => [],
                ];
            }
            
            $grouped[$groupKey]['options'][$item->id] = $options[$item->id];
        }
        
        // Sort ungrouped to the end
        if (isset($grouped['_ungrouped'])) {
            $ungrouped = $grouped['_ungrouped'];
            unset($grouped['_ungrouped']);
            $grouped['_ungrouped'] = $ungrouped;
        }
        
        return $grouped;
    }

    /**
     * Get all options for search and bulk toggle functionality.
     */
    public function getFlatOptions(): array
    {
        return $this->getOptions();
    }
}
