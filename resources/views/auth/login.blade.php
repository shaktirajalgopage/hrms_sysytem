@extends('layouts.auth')

@section('title')
  {{ __('Login') }}
@endsection

@section('header')
  {{-- Intentionally left empty: the 3D card below carries its own header --}}
@endsection

@section('content')

<style>
  /* ============================================================
     3D LOGIN — self-contained styles
     Palette: deep plum-charcoal stage, satin-amber primary accent,
     periwinkle secondary accent. Signature: a stacked "deck of
     glass panes" card that tilts in real 3D as the cursor moves,
     plus a spinning conic-gradient halo behind the avatar.
  ============================================================ */
  @import url('https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&display=swap');

  :root{
    --stage-1:#150f24;
    --stage-2:#1f1638;
    --amber:#f0b429;
    --amber-soft:#f7d38c;
    --periwinkle:#8ea6ff;
    --glass:rgba(255,255,255,.06);
    --glass-border:rgba(255,255,255,.14);
    --ink:#f3f0ff;
    --ink-dim:#a9a2c9;
  }

  .auth3d-stage{
    position:relative;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:3rem 1.25rem;
    overflow:hidden;
    background:
      radial-gradient(1100px 700px at 15% 10%, #2a1f4d 0%, transparent 60%),
      radial-gradient(900px 600px at 90% 90%, #2c1c3d 0%, transparent 55%),
      linear-gradient(160deg, var(--stage-1), var(--stage-2) 70%);
    font-family:'Sora', system-ui, sans-serif;
  }

  /* Ambient depth blobs — blurred = far, card = near */
  .auth3d-blob{
    position:absolute;
    border-radius:50%;
    filter:blur(60px);
    opacity:.45;
    animation:auth3d-float 9s ease-in-out infinite alternate;
    pointer-events:none;
  }
  .auth3d-blob.b1{ width:340px; height:340px; top:-80px; left:-100px; background:var(--periwinkle); }
  .auth3d-blob.b2{ width:280px; height:280px; bottom:-90px; right:-60px; background:var(--amber); animation-delay:1.2s; }
  .auth3d-blob.b3{ width:180px; height:180px; bottom:20%; left:6%; background:#7c3aed; opacity:.3; animation-delay:2.4s; }

  @keyframes auth3d-float{
    from{ transform:translateY(0) scale(1); }
    to{ transform:translateY(-26px) scale(1.06); }
  }

  /* Perspective wrapper that hosts the tilting card */
  .auth3d-perspective{
    perspective:1400px;
    width:100%;
    max-width:440px;
    position:relative;
    z-index:1;
  }

  /* Stack of "glass panes" behind the main card for real depth */
  .auth3d-deck{ position:relative; }
  .auth3d-deck::before,
  .auth3d-deck::after{
    content:'';
    position:absolute;
    inset:0;
    border-radius:28px;
    background:var(--glass);
    border:1px solid var(--glass-border);
    z-index:0;
  }
  .auth3d-deck::before{ transform:translate3d(14px,16px,-40px) rotate(-3deg); opacity:.55; }
  .auth3d-deck::after{ transform:translate3d(-10px,10px,-70px) rotate(2.5deg); opacity:.35; }

  .auth3d-card{
    position:relative;
    z-index:1;
    border-radius:28px;
    padding:2.75rem 2.25rem 2.25rem;
    background:linear-gradient(160deg, rgba(255,255,255,.09), rgba(255,255,255,.03));
    border:1px solid var(--glass-border);
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    box-shadow:
      0 30px 60px -20px rgba(0,0,0,.55),
      0 2px 0 rgba(255,255,255,.06) inset;
    transform-style:preserve-3d;
    transition:transform .25s ease-out;
    will-change:transform;
  }

  /* Halo + avatar */
  .auth3d-avatar-wrap{
    width:112px; height:112px;
    margin:0 auto 1.5rem;
    position:relative;
    transform:translateZ(50px);
  }
  .auth3d-avatar-wrap::before{
    content:'';
    position:absolute;
    inset:-8px;
    border-radius:50%;
    background:conic-gradient(from 0deg, var(--amber), var(--periwinkle), #7c3aed, var(--amber));
    animation:auth3d-spin 6s linear infinite;
    filter:blur(1px);
  }
  .auth3d-avatar-wrap img{
    position:relative;
    width:100%; height:100%;
    border-radius:50%;
    object-fit:cover;
    border:4px solid var(--stage-2);
    display:block;
    box-shadow:0 14px 30px rgba(0,0,0,.5);
  }
  @keyframes auth3d-spin{ to{ transform:rotate(360deg); } }

  .auth3d-eyebrow{
    text-align:center;
    font-size:.72rem;
    font-weight:700;
    letter-spacing:.14em;
    text-transform:uppercase;
    color:var(--amber-soft);
    transform:translateZ(40px);
  }
  .auth3d-title{
    text-align:center;
    font-weight:800;
    font-size:1.75rem;
    color:var(--ink);
    margin:.35rem 0 .35rem;
    transform:translateZ(45px);
  }
  .auth3d-sub{
    text-align:center;
    color:var(--ink-dim);
    font-size:.9rem;
    margin-bottom:1.75rem;
    transform:translateZ(35px);
  }

  .auth3d-field{ margin-bottom:1.15rem; transform:translateZ(25px); }
  .auth3d-field label{
    display:block;
    font-size:.78rem;
    font-weight:600;
    color:var(--ink-dim);
    margin-bottom:.4rem;
    letter-spacing:.02em;
  }
  .auth3d-field .form-control{
    background:rgba(0,0,0,.22);
    border:1px solid var(--glass-border);
    color:var(--ink);
    border-radius:14px;
    padding:.85rem 1rem;
    font-size:.95rem;
    box-shadow:inset 0 2px 6px rgba(0,0,0,.35);
    transition:box-shadow .2s ease, border-color .2s ease;
  }
  .auth3d-field .form-control::placeholder{ color:#736a94; }
  .auth3d-field .form-control:focus{
    outline:none;
    border-color:var(--amber);
    box-shadow:inset 0 2px 6px rgba(0,0,0,.35), 0 0 0 3px rgba(240,180,41,.22);
    background:rgba(0,0,0,.3);
  }

  .auth3d-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin:.25rem 0 1.6rem;
    transform:translateZ(20px);
    flex-wrap:wrap;
    gap:.5rem;
  }
  .auth3d-row .form-check-label{ color:var(--ink-dim); font-size:.85rem; }
  .auth3d-row .form-check-input{
    background-color:rgba(0,0,0,.3);
    border:1px solid var(--glass-border);
  }
  .auth3d-row .form-check-input:checked{
    background-color:var(--amber);
    border-color:var(--amber);
  }
  .auth3d-row a{
    color:var(--periwinkle);
    font-size:.85rem;
    text-decoration:none;
    font-weight:600;
  }
  .auth3d-row a:hover{ color:var(--amber-soft); }

  .auth3d-submit{
    width:100%;
    border:none;
    border-radius:14px;
    padding:.95rem 1rem;
    font-weight:700;
    font-size:1rem;
    letter-spacing:.02em;
    color:#241a06;
    background:linear-gradient(135deg, var(--amber-soft), var(--amber) 60%, #d9950f);
    box-shadow:
      0 14px 26px -10px rgba(240,180,41,.55),
      0 1px 0 rgba(255,255,255,.4) inset;
    transform:translateZ(30px);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .auth3d-submit:hover{ transform:translateZ(30px) translateY(-2px); }
  .auth3d-submit:active{
    transform:translateZ(30px) translateY(1px);
    box-shadow:0 6px 14px -8px rgba(240,180,41,.55), 0 1px 0 rgba(255,255,255,.3) inset;
  }

  @media (max-width:480px){
    .auth3d-card{ padding:2.25rem 1.5rem 1.75rem; }
    .auth3d-title{ font-size:1.5rem; }
  }

  @media (prefers-reduced-motion: reduce){
    .auth3d-blob, .auth3d-avatar-wrap::before{ animation:none; }
    .auth3d-card{ transition:none; }
  }
</style>

<div class="auth3d-stage">
  <div class="auth3d-blob b1"></div>
  <div class="auth3d-blob b2"></div>
  <div class="auth3d-blob b3"></div>

  <div class="auth3d-perspective">
    <div class="auth3d-deck">
      <div class="auth3d-card" id="auth3dCard">

        <div class="auth3d-avatar-wrap">
          <img src="{{ asset('img/avatars/dummy.png') }}" alt="Charles Hall" />
        </div>

        <p class="auth3d-eyebrow">{{ __('Secure access') }}</p>
        <h1 class="auth3d-title">{{ __('Welcome back') }}</h1>
        <p class="auth3d-sub">{{ __('Sign in to your account to continue') }}</p>

        <div style="transform:translateZ(25px);">
          <x-auth-session-status class="mb-3" :status="session('status')" />
        </div>

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <div class="auth3d-field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" class="form-control form-control-lg" type="email" name="email" placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}" required autofocus />
          </div>

          <div class="auth3d-field">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" class="form-control form-control-lg" type="password" name="password" placeholder="{{ __('Enter your password') }}" required />
          </div>

          <div class="auth3d-row">
            <label class="form-check d-flex align-items-center gap-2 mb-0">
              <input class="form-check-input" type="checkbox" value="remember-me" name="remember-me" checked>
              <span class="form-check-label">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
          </div>

          <button type="submit" class="auth3d-submit">{{ __('Sign in') }}</button>
        </form>

      </div>
    </div>
  </div>
</div>

<script>
  (function(){
    var card = document.getElementById('auth3dCard');
    var stage = card.closest('.auth3d-perspective');
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced || !card || !stage) return;

    var maxTilt = 8; // degrees

    stage.addEventListener('mousemove', function(e){
      var rect = stage.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width;  // 0 -> 1
      var y = (e.clientY - rect.top) / rect.height;   // 0 -> 1
      var rotateY = (x - 0.5) * maxTilt * 2;
      var rotateX = (0.5 - y) * maxTilt * 2;
      card.style.transform = 'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
    });

    stage.addEventListener('mouseleave', function(){
      card.style.transform = 'rotateX(0deg) rotateY(0deg)';
    });
  })();
</script>

@endsection