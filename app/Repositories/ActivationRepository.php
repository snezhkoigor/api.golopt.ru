<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 13:30
 */

namespace App\Repositories;


use Carbon\Carbon;
use Illuminate\Database\Connection;

class ActivationRepository
{
    protected $db;

    protected $table = 'user_activations';

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create($user)
    {
        $activation = $this->get($user);

        if (!$activation) {
            return $this->createToken($user);
        }

        return $this->regenerateToken($user);
    }

    public function get($user)
    {
        return $this->db->table($this->table)->where('user_id', $user->id)->first();
    }

    public function getByToken($token)
    {
        return $this->db->table($this->table)->where('token', $token)->first();
    }

    public function delete($token)
    {
        $this->db->table($this->table)->where('token', $token)->delete();
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

    private function createToken($user)
    {
        $token = $this->getToken();
        $this->db->table($this->table)->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => new Carbon()
        ]);

        return $token;
    }
}