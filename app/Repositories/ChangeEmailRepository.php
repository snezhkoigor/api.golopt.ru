<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 03.08.17
 * Time: 17:58
 */

namespace App\Repositories;


use Carbon\Carbon;
use Illuminate\Database\Connection;

class ChangeEmailRepository
{
    protected $db;

    protected $table = 'user_change_emails';

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create($user, $new)
    {
        $activation = $this->get($user, $new);

        if (!$activation) {
            return $this->createToken($user, $new);
        }

        return $this->regenerateToken($user);
    }

    public function get($user, $newEmail)
    {
        return $this->db->table($this->table)->where([
            ['active', 1],
            ['user_id', $user->id],
            ['new', $newEmail]
        ])->first();
    }

    public function getByToken($token)
    {
        return $this->db->table($this->table)->where([
            ['active', 1],
            ['token', $token]
        ])->first();
    }

    public function delete($token)
    {
        $this->db->table($this->table)->where('token', $token)->update([
            'active' => 0
        ]);
    }

    protected function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    private function regenerateToken($user)
    {
        $token = $this->getToken();
        $this->db->table($this->table)->where('user_id', $user->id)->update([
            'token' => $token,
            'created_at' => new Carbon()
        ]);

        return $token;
    }

    private function createToken($user, $new)
    {
        $token = $this->getToken();
        $this->db->table($this->table)->insert([
            'user_id' => $user->id,
            'token' => $token,
            'old' => $user->email,
            'new' => $new,
            'created_at' => new Carbon()
        ]);

        return $token;
    }
}