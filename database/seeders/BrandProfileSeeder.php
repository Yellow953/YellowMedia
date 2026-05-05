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

IMAGE VISUAL STYLE (follow this precisely):
- BACKGROUND: Either (A) pure black/very dark background with dramatic golden glowing light rays and floating gold particle sparkles, OR (B) solid bright yellow (#F5C300) background. Never generic backgrounds, gradients, or stock imagery backdrops.
- HERO SUBJECT: A high-quality, photorealistic product shot specific to the industry being targeted — e.g. a burger for restaurants, perfume bottle for fragrance shops, supplement jars for gyms, grocery bag with vegetables for supermarkets, beauty products for cosmetics. The product should be large, premium, and cinematic.
- PHONE MOCKUP: A modern smartphone showing the YellowPOS app UI (yellow interface, product listings, order confirmation). The phone and the hero product are the two main elements. Products often appear to burst or float out of the phone screen with golden sparkle particle effects.
- TYPOGRAPHY STYLE: Bold, heavy, all-caps headline text. White text on dark backgrounds. Black or dark text on yellow backgrounds. Short powerful phrases: "TURN SCENTS INTO SALES", "BE FOUND. BE READY.", "POWER YOUR SALES."
- FEATURE ICONS: Small row of yellow icons representing: fast delivery, WhatsApp, QR code, online ordering. Minimal and clean.
- MOOD: High-energy, premium commercial advertisement aesthetic. Think global brand ad meets Lebanese street energy. Dramatic lighting, sharp contrasts.
- COLOR PALETTE: Yellow #F5C300, black #111111, white. Golden glow accents. No other colors dominate.
EOT,
            ]
        );

        $this->command->info("Brand profile seeded for tenant: {$tenant->name}");
    }
}
