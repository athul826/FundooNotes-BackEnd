<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Notes extends Model implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_id()
    {
        return $this->belongsTo(User::class);
    }


    public static function getNotesByNoteIdandUserId($id, $user_id)
    {
        $notes = Notes::where('id', $id)->where('user_id', $user_id)->first();
        return $notes;
    }

    public function noteId($id)
    {
        return Notes::where('id', $id)->first();
    }

    // 
    // public static function getAllNotes($user)
    // {
    //     $note = User::leftjoin('notes', 'notes.user_id', '=', 'users.id')
    //         ->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
    //         ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')

    //         ->select('users.id', 'notes.id', 'notes.title', 'notes.description', 'lables.labelname')
    //         ->where([['notes.user_id', '=', $user->id]])
    //         ->get();
    //     return $note;
    // }
    public static function getPinnedNotesandItsLabels($user)
    {
        $notes = Notes::leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('notes.id', 'notes.title', 'notes.description', 'notes.isPinned', 'notes.isArchived', 'notes.colour', 'lables.labelname')
            ->where([['notes.user_id', '=', $user->id], ['isPinned', '=', 1]]) ->get();

        return $notes;
    }

     /**
     * Function to get the archived notes and their labels
     * Passing the user as a parameter
     * 
     * @return array
     */
    public static function getArchivedNotesandItsLabels($user)
    {
        $notes = Notes::leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('notes.id', 'notes.title', 'notes.description', 'notes.isPinned', 'notes.isArchived', 'notes.colour', 'lables.labelname')
            ->where([['notes.user_id', '=', $user->id], ['isArchived', '=', 1]]) ->get();

        return $notes;
    }

    
    public static function getAllNotes($user)
    {
        $note = User::leftjoin('notes', 'notes.user_id', '=', 'users.id')
            ->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')

            ->select('users.id', 'notes.id', 'notes.title', 'notes.description', 'notes.isPinned', 'notes.isArchived', 'notes.colour', 'lables.labelname')
            ->where([['notes.user_id', '=', $user->id],['isArchived', '=', 0], ['isPinned', '=', 0],['isTrashed', '=', 0]])
            ->get();


        return $note;
    }
    // public static function getAllNotes($user)
    // {
    //     $note = User::leftjoin('notes', 'notes.user_id', '=', 'users.id')
    //         ->leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
    //         ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')

    //         ->select('users.id', 'notes.id', 'notes.title', 'notes.description', 'lables.labelname')
    //         ->where([['notes.user_id', '=', $user->id]])
    //         ->get();
    //     return $note;
    // }

    public static function getTrashedNotesandItsLabels($user)
    {
        $notes = Notes::leftJoin('label_notes', 'label_notes.note_id', '=', 'notes.id')
            ->leftJoin('lables', 'lables.id', '=', 'label_notes.label_id')
            ->select('notes.id', 'notes.title', 'notes.description', 'notes.isPinned', 'notes.isArchived', 'notes.colour', 'lables.labelname')
            ->where([['notes.user_id', '=', $user->id],['isArchived', '=', 0], ['isPinned', '=', 0], ['isTrashed', '=', 1]]) ->get();

        return $notes;
    }
}
