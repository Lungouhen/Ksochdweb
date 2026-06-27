<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case MEMBER = 'member';
    case VOLUNTEER = 'volunteer';
    case DONOR = 'donor';
    
    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::ADMIN => 'Administrator',
            self::STAFF => 'Staff Member',
            self::MEMBER => 'Member',
            self::VOLUNTEER => 'Volunteer',
            self::DONOR => 'Donor',
        };
    }
    
    public function isAdmin(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN, self::STAFF]);
    }
}
