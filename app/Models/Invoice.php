<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'donation_id',
        'membership_id',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'paid_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public static function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
}
echo "Campaign, Donation, RecurringDonation, Invoice models created"
cd /workspace && cat > app/Models/Event.php << 'EOF'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'content', 'category_id',
        'location', 'address', 'city', 'state', 'country', 'zip_code',
        'is_online', 'online_url', 'starts_at', 'ends_at', 'capacity',
        'registered_count', 'price', 'status', 'is_featured',
        'allow_registration', 'registration_deadline', 'metadata', 'seo',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'registration_deadline' => 'datetime',
        'is_online' => 'boolean',
        'is_featured' => 'boolean',
        'allow_registration' => 'boolean',
        'metadata' => 'array',
        'seo' => 'array',
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function registrations(): HasMany { return $this->hasMany(EventRegistration::class); }
    public function volunteerOpportunities(): HasMany { return $this->hasMany(VolunteerOpportunity::class); }

    public function scopeActive($query) {
        return $query->where('status', 'published')->where('starts_at', '>=', now());
    }
    
    public function isRegistrationOpen(): bool {
        return $this->allow_registration && 
            ($this->registration_deadline === null || $this->registration_deadline->isFuture()) &&
            ($this->capacity === null || $this->registered_count < $this->capacity);
    }
}
