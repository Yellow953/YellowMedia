<?php

namespace Database\Seeders;

use App\Models\BrandProfile;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class BrandProfileSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (! $tenant) {
            $this->command->warn('No tenant found. Run the main seeder first.');
            return;
        }

        BrandProfile::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'business_name'        => 'YellowPOS',
                'instagram_handle'     => 'yellowpos_com',
                'brand_colors'         => 'Yellow #F5C300 and Black #111111. White backgrounds.',
                'target_audience'      => 'Lebanese small and medium business owners — restaurants, cafes, retail shops, salons, and service businesses looking to manage orders, bookings, and payments in one place.',
                'tone'                 => 'bold',
                'content_pillars'      => [
                    'Product features & how-tos',
                    'Business growth tips',
                    'Success stories & testimonials',
                    'Lebanese business culture',
                    'Promotions & seasonal campaigns',
                ],
                'hashtags'             => "#YellowPOS #Lebanon #لبنان #POS #OnlineStore #Restaurant #Retail #محل #مطعم #لبنان",
                'business_description' => 'YellowPOS is an all-in-one business OS for Lebanese businesses. It combines a POS system, online store, digital menu, table reservations, online bookings, WhatsApp orders, delivery tracking (Google Maps), dual currency support (USD/LBP), reports & analytics, and tools like invoice/barcode generators — all in one platform. Tagline: "Turn Clicks into Customers". Positioned as the Shopify alternative built for Lebanon, serving 1000+ online stores.',
                'sample_captions'      => implode("\n\n---\n\n", [
                    "📲 Your store. Your rules. Your customers.\nManage orders, track inventory, and accept payments — all from one dashboard.\nStart free today 👉 yellow-pos.com\n#YellowPOS #Lebanon #OnlineStore #لبنان",

                    "بدك تفتح محلك أونلاين؟ 🛒\nمع YellowPOS بتقدر تبني متجرك الإلكتروني بدقائق.\nقبول دفع، إدارة طلبات، تتبع مخزون — كل شي بمكان واحد.\n#YellowPOS #لبنان #محل_اونلاين",

                    "Tired of juggling WhatsApp, Instagram DMs, and cash at the same time? 😮‍💨\nYellowPOS centralizes everything so you can focus on what matters — growing your business.\n📍 Designed for Lebanon. Built for you.\n#YellowPOS #SMB #Lebanon",

                    "💡 Did you know? You can accept USD and LBP at the same time on YellowPOS.\nNo more manual calculations. No more confusion at checkout.\nYour customers pay how they want. You get paid either way. 💛\n#YellowPOS #Lebanon #DualCurrency #لبنان",

                    "مطعمك يستاهل أكتر من ورق الطلبيات 📋\nمع YellowPOS:\n✅ قائمة رقمية QR\n✅ حجز طاولات أونلاين\n✅ إدارة طلبات الديليفري\n✅ تقارير يومية\nجرّب مجاناً 👇\n#YellowPOS #مطعم #لبنان #Restaurant",
                ]),
                'voice_summary'        => <<<'EOT'
CAPTION VOICE: Bold and direct, speaking to Lebanese business owners like a peer. Short punchy sentences, specific benefits, zero fluff. Mix Arabic and English naturally in the same post — often a bold Arabic headline with an English sub-line or vice versa. 1-2 emojis max. CTAs are action-first: "Start free today", "جرّب مجاناً", "ابدأ الآن", "GO DIGITAL". Hashtags always include #YellowPOS #Lebanon #لبنان plus a niche tag. Tie every feature to a real Lebanese business pain (dual currency chaos, WhatsApp orders, cash payments, power cuts).
IMAGE VISUAL STYLE:
BRAND PALETTE: Yellow #F5C300, black #111111, white. These three colors are the only dominant palette. Golden glow accents are optional.
BACKGROUND — rotate between these options, do NOT always use dark/black:
- Pure black/dark background with dramatic golden light rays and floating gold particle sparkles
- Solid bright yellow (#F5C300) background with black or white text
- Clean white or off-white background with yellow and black accents
- Split layout: left half yellow + right half black (or vice versa)
- Dark background with a spotlight/studio light effect on the hero
HERO SUBJECT: A high-quality, photorealistic product shot specific to the industry — a burger, perfume bottle, supplement jars, grocery bag, beauty products. Large, premium, cinematic. Photorealistic, not 3D-rendered cartoon style.
PHONE MOCKUP: Optional — only include a smartphone mockup when it adds to the concept. Do NOT default to including a phone in every single image. Many great designs have no phone at all.
TYPOGRAPHY: Bold, heavy, all-caps or mixed-case headlines. White on dark backgrounds. Black or dark text on yellow/white backgrounds. Short powerful phrases.
LAYOUT — vary between these styles per generation:
- Large bold typographic headline dominating top third, product below
- Hero product close-up filling 60% of frame with text on the side
- Icon grid — feature icons in a clean row with short labels
- Split panel — two distinct zones for visual and text
- Flat lay top-down composition
- Stat or testimonial card with a big number or quote as focal point
- Lifestyle or in-context scene (business owner using the product)
MOOD: High-energy, premium commercial advertisement. Dramatic lighting, sharp contrasts. Think global brand ad meets Lebanese street energy.
IMPORTANT: Do NOT include any brand name, logo, or wordmark. Do NOT repeat the same phone + dark background + sparkles layout every time.
EOT,
            ]
        );

        $this->command->info("Brand profile seeded for tenant: {$tenant->name}");
    }
}
