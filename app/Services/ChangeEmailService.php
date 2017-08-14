<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 03.08.17
 * Time: 17:56
 */

namespace App\Services;


use App\Mail\ChangeEmail;
use App\Repositories\ChangeEmailRepository;
use App\User;
use Illuminate\Support\Facades\Mail;

class ChangeEmailService
{
    protected $changeEmailRepo;
    private $resendAfter = 24;

    public function __construct(ChangeEmailRepository $changeEmailRepo)
    {
        $this->changeEmailRepo = $changeEmailRepo;
    }

    public function sendMail($user, $newEmail)
    {
        if (!$this->shouldSend($user, $newEmail)) {
            return;
        }

        $this->changeEmailRepo->create($user, $newEmail);

        Mail::to($newEmail)->send(new ChangeEmail($user));
    }

    public function change($token)
    {
        $record = $this->changeEmailRepo->getByToken($token);

        if ($record === null) {
            return null;
        }

        $user = User::find($record->user_id);
            $user->email = $record->new;
        $user->save();

        $this->changeEmailRepo->delete($token);
        Mail::to($user->email)->send(new ChangeEmail($user, false));

        return $user;
    }

    public function delete($token)
    {
        return $this->changeEmailRepo->delete($token);
    }

    public function get($user, $newEmail)
    {
        return $this->changeEmailRepo->get($user, $newEmail);
    }

    public function getByToken($token)
    {
        return $this->changeEmailRepo->getByToken($token);
    }

    private function shouldSend($user, $newEmail)
    {
        $record = $this->changeEmailRepo->get($user, $newEmail);

        return $record === null || strtotime($record->created_at) + 60 * 60 * $this->resendAfter < time();
    }
}