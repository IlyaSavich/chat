<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Room
 *
 * @property int $id
 * @property string|null $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Room extends Model
{
    public const TYPE_PUBLIC = 'public';
    public const TYPE_DIALOG = 'dialog';

    protected $fillable = [
        'name',
        'type',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_user');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
