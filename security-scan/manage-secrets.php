<?php

/**
 * Secrets Management Helper
 * This script helps manage secrets securely
 */

class SecretsManager {
    private $secretsFile = '.secrets.encrypted';
    private $keyFile = '.key';
    
    public function encrypt($data, $key) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt(
            json_encode($data),
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        return base64_encode($iv . $encrypted);
    }
    
    public function decrypt($data, $key) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $decrypted = openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        return json_decode($decrypted, true);
    }
    
    public function storeSecrets($secrets) {
        $key = bin2hex(openssl_random_pseudo_bytes(32));
        $encrypted = $this->encrypt($secrets, $key);
        
        file_put_contents($this->secretsFile, $encrypted);
        file_put_contents($this->keyFile, $key);
        
        chmod($this->secretsFile, 0600);
        chmod($this->keyFile, 0600);
        
        echo "Secrets encrypted and stored securely.\n";
        echo "Key file: {$this->keyFile}\n";
        echo "Secrets file: {$this->secretsFile}\n";
        echo "\nIMPORTANT: Store the key file separately from the secrets!\n";
    }
    
    public function loadSecrets() {
        if (!file_exists($this->keyFile) || !file_exists($this->secretsFile)) {
            throw new Exception("Secrets or key file not found");
        }
        
        $key = file_get_contents($this->keyFile);
        $encrypted = file_get_contents($this->secretsFile);
        
        return $this->decrypt($encrypted, $key);
    }
}

// Example usage (commented out for safety)
// $manager = new SecretsManager();
// $secrets = [
//     'db_password' => 'your-secure-password',
//     'api_keys' => [
//         'openai' => 'your-api-key',
//         // ... other keys
//     ]
// ];
// $manager->storeSecrets($secrets);
