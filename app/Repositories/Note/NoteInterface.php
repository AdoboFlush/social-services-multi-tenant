<?php

namespace App\Repositories\Note;

use App\Repositories\BaseInterface;

interface NoteInterface extends BaseInterface
{
  public function getNote(string $note_slug): ?string;
}
