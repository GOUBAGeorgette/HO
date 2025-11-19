<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Equipment;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'is_active'
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir les équipements associés à cette catégorie.
     */
    /**
     * Obtenir les équipements associés à cette catégorie.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Obtenir la catégorie parente.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Obtenir les sous-catégories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Obtenir les ancêtres directs (relation récursive).
     * Retourne une collection des ancêtres de la catégorie.
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors->reverse();
    }

    /**
     * Obtenir toutes les catégories parentes (récursif).
     */
    public function allParents()
    {
        if (!$this->parent) {
            return collect();
        }
        
        return collect([$this->parent])->merge($this->parent->allParents());
    }

    /**
     * Obtenir toutes les sous-catégories (récursif).
     */
    public function allChildren()
    {
        return $this->children->flatMap(function ($child) {
            return $child->allChildren();
        })->prepend($this);
    }
    
    /**
     * Vérifie si la catégorie est une catégorie racine.
     *
     * @return bool
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }
    
    /**
     * Vérifie si la catégorie est un descendant de la catégorie donnée.
     *
     * @param  \App\Models\Category  $category
     * @return bool
     */
    public function isDescendantOf(Category $category)
    {
        $parent = $this->parent;
        
        while ($parent) {
            if ($parent->id === $category->id) {
                return true;
            }
            $parent = $parent->parent;
        }
        
        return false;
    }
    
    /**
     * Vérifie si la catégorie peut être supprimée.
     * Une catégorie peut être supprimée si elle n'a pas d'équipements associés.
     *
     * @return bool
     */
    public function canBeDeleted()
    {
        return $this->equipment()->count() === 0;
    }
}
