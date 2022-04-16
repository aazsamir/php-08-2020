<?php

namespace App\Library\Auth;

/**
 * Auth request across all systems (based on config)
 */
class SystemAuth
{
    /**
     * Singleton
     * @var SystemAuth
     */
    static $singleton;

    /**
     * 
     * @var AuthInterface[]
     */
    protected $systems = [];

    /**
     * Matched system of authorization
     * @var string
     */
    protected $matched_system;

    public static function getInstance(...$args)
    {
        if (!static::$singleton) {
            static::$singleton = new static(...$args);
        }

        return static::$singleton;
    }

    public function __construct()
    {
        $config = config('systems', []);
        foreach ($config as $system => $data) {
            if (!isset($data['auth'])) {
                continue;
            }
            $this->systems[$system] = new $data['auth'];
        }
    }

    /**
     * Auth user
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public function auth($login, $password)
    {
        foreach ($this->systems as $name => $system) {
            /** @var AuthInterface $system */
            $match = $system->match($login);
            if ($match) {
                $this->matched_system = $name;
                return $system->action($login, $password);
            }
        }

        return false;
    }

    /**
     * Get matched system
     * @return string
     */
    public function system()
    {
        return $this->matched_system;
    }

    /**
     * Generate JWT with encoded login and system
     * @param string $login
     * @param string $system
     * @return string
     */
    public function generateToken($login, $system = null)
    {
        $system = $system ?: $this->matched_system;

        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256',
        ]);

        $payload = json_encode([
            'login' => $login,
            'system' => $system
        ]);

        $base_header = base64_encode($header);
        $base_payload = base64_encode($payload);

        $signature = hash_hmac('sha256', $base_header . '.' . $base_payload, 'foo-bar-baz', true);
        $base_signature = base64_encode($signature);

        $jwt = $this->base64url($base_header . '.' . $base_payload . '.' . $base_signature);
        return $jwt;
    }

    /**
     * Make URL friendly base64
     * @param string $base64
     * @return string
     */
    protected function base64url($base64)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], $base64);
    }
}
