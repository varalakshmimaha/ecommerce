@extends('layouts.frontend')

@section('title', 'Services')

@section('content')

<style>
    /* ── Banner ── */
    .svc-banner{position:relative;width:100%;height:420px;overflow:hidden}
    .svc-banner-img{width:100%;height:100%;object-fit:cover;object-position:center;display:block}
    .svc-banner-overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(10,10,30,.72) 0%,rgba(139,0,0,.45) 60%,rgba(10,10,30,.6) 100%)}
    .svc-banner-content{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:0 24px}
    .svc-banner-label{display:inline-block;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#D4AF37;background:rgba(212,175,55,.15);border:1px solid rgba(212,175,55,.4);padding:6px 20px;border-radius:50px;margin-bottom:18px}
    .svc-banner-content h1{font-size:48px;font-weight:900;color:#fff;margin-bottom:14px;line-height:1.15;text-shadow:0 2px 16px rgba(0,0,0,.4)}
    .svc-banner-content h1 span{color:#fff;-webkit-text-fill-color:#fff}
    .svc-banner-content p{font-size:16px;color:rgba(255,255,255,.75);max-width:560px;line-height:1.75}
    .svc-banner-bar{position:absolute;bottom:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#D4AF37,#8B0000,#D4AF37)}
    @media(max-width:768px){.svc-banner{height:300px}.svc-banner-content h1{font-size:32px}}
    @media(max-width:480px){.svc-banner{height:240px}.svc-banner-content h1{font-size:24px}.svc-banner-content p{font-size:14px}}

    /* ── Grid ── */
    .svc-section{padding:60px 0 80px;background:#f9fafb}
    .svc-container{max-width:1200px;margin:0 auto;padding:0 24px}
    .svc-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px}
    @media(max-width:1024px){.svc-grid{grid-template-columns:repeat(3,1fr)}}
    @media(max-width:768px){.svc-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:480px){.svc-grid{grid-template-columns:1fr}}

    .svc-card{background:#fff;border-radius:16px;overflow:hidden;border:1px solid #eee;transition:all .3s ease;position:relative;cursor:pointer}
    .svc-card:hover{transform:translateY(-6px);box-shadow:0 16px 40px rgba(0,0,0,.1);border-color:transparent}
    .svc-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#D4AF37,#8B0000);transform:scaleX(0);transition:transform .3s ease;transform-origin:center}
    .svc-card:hover::after{transform:scaleX(1)}
    .svc-card-img{width:100%;height:180px;overflow:hidden}
    .svc-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
    .svc-card:hover .svc-card-img img{transform:scale(1.08)}
    .svc-card-body{padding:18px 18px 20px}
    .svc-card-tag{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#D4AF37;margin-bottom:8px}
    .svc-card-tag i{font-size:11px}
    .svc-card-body h3{font-size:15px;font-weight:700;color:#1a1a1a;margin-bottom:7px;line-height:1.3}
    .svc-card-body p{font-size:13px;color:#6b7280;line-height:1.65}
</style>

<!-- Banner -->
<div class="svc-banner">
    <img class="svc-banner-img" src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1600&q=85" alt="Advertising Services Banner">
    <div class="svc-banner-overlay"></div>
    <div class="svc-banner-content">
        <div class="svc-banner-label">Grow Your Brand</div>
        <h1><span>Advertising</span> Services</h1>
        <p>Complete advertising solutions to boost your business visibility and reach the right audience.</p>
    </div>
    <div class="svc-banner-bar"></div>
</div>

<!-- Services Grid -->
<div class="svc-section">
    <div class="svc-container">
        <div class="svc-grid">

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1504270997636-07ddfbd48945?w=600&q=80" alt="Print Advertisement"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-newspaper"></i> Print</div>
                    <h3>Print Advertisement</h3>
                    <p>Newspapers, magazines & flyers with wide regional reach</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=600&q=80" alt="Direct BC Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-id-card"></i> Business Cards</div>
                    <h3>Direct BC Ads</h3>
                    <p>Targeted business card campaigns for local businesses</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1526554850534-7c78330d5f90?w=600&q=80" alt="Direct Mail"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-envelope"></i> Direct Mail</div>
                    <h3>Direct Mail</h3>
                    <p>Physical mailers delivered directly to your audience</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1478737270239-2f02b77fc618?w=600&q=80" alt="Podcast Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-microphone"></i> Podcast</div>
                    <h3>Podcast Ads</h3>
                    <p>Reach engaged listeners through curated podcast spots</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600&q=80" alt="Mobile Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-mobile-alt"></i> Mobile</div>
                    <h3>Mobile Ads</h3>
                    <p>SMS, in-app & push notification campaigns</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600&q=80" alt="Social Media"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-share-alt"></i> Social Media</div>
                    <h3>Social Media</h3>
                    <p>Facebook, Instagram, YouTube & more — all managed</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&q=80" alt="Paid Search"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-search-dollar"></i> PPC</div>
                    <h3>Paid Search (PPC)</h3>
                    <p>Google Ads & Bing — top of search results</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=600&q=80" alt="Native Advertising"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-file-alt"></i> Native</div>
                    <h3>Native Advertising</h3>
                    <p>Seamlessly blend ads into editorial content</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&q=80" alt="Display Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-desktop"></i> Display</div>
                    <h3>Display Ads</h3>
                    <p>Banner & rich media across premium networks</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?w=600&q=80" alt="Outdoor Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-map-marker-alt"></i> Outdoor</div>
                    <h3>Outdoor Ads</h3>
                    <p>Hoardings, bus stops & out-of-home placements</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=600&q=80" alt="Guerrilla Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-bolt"></i> Guerrilla</div>
                    <h3>Guerrilla Ads</h3>
                    <p>Creative street-level activations that stop people</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600&q=80" alt="Product Placement"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-film"></i> Product Placement</div>
                    <h3>Product Placement</h3>
                    <p>Feature your brand in videos & online content</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=600&q=80" alt="Public Service Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-hands-helping"></i> Public Service</div>
                    <h3>Public Service Ads</h3>
                    <p>Build trust through community-focused campaigns</p>
                </div>
            </div>

            <div class="svc-card">
                <div class="svc-card-img"><img src="https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=600&q=80" alt="Programmatic Ads"></div>
                <div class="svc-card-body">
                    <div class="svc-card-tag"><i class="fas fa-robot"></i> Programmatic</div>
                    <h3>Programmatic Ads</h3>
                    <p>AI-driven automated ad buying across thousands of sites</p>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
