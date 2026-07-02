<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetVCSignaturePin extends Command
{
    protected $signature = 'vc:set-pin {email} {pin}';
    protected $description = 'Set digital signature PIN for Vice Chancellor';

    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->first();
        
        if (!$user) {
            $this->error('User not found!');
            return 1;
        }
        
        $user->signature_pin = Hash::make($this->argument('pin'));
        $user->save();
        
        $this->info('Signature PIN set successfully for ' . $user->email);
        return 0;
    }
}