<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengingat Jurnal SiHebat</title>
</head>
<body style="font-family: 'Plus Jakarta Sans', Arial, sans-serif; background-color: #F0F9FF; margin: 0; padding: 24px; color: #0F2A43;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #FFFFFF; border-radius: 20px; padding: 28px; border: 1px solid #BAE6FD; box-shadow: 0 4px 12px rgba(14,165,233,0.08);">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #0284C7; font-size: 22px; margin: 0; font-weight: 800;">SiHebat 🌤️</h1>
            <p style="color: #6B829A; font-size: 12px; margin: 4px 0 0 0;">7 Kebiasaan Anak Indonesia Hebat • SMAN 1 Sungailiat</p>
        </div>

        <div style="background-color: #F0F9FF; border-radius: 16px; padding: 18px; margin-bottom: 20px; border-left: 4px solid #0EA5E9;">
            <p style="font-size: 15px; font-weight: 700; margin: 0 0 8px 0; color: #0C4A6E;">
                Halo, {{ $student->name }}! 👋
            </p>
            <p style="font-size: 13px; line-height: 1.5; margin: 0; color: #35506B;">
                Hari ini kamu belum mencatatkan jurnal 7 Kebiasaan di SiHebat. Jangan biarkan streak konsistensimu putus, ya!
            </p>
        </div>

        <div style="background-color: #FFF4CC; border-radius: 12px; padding: 12px 16px; margin-bottom: 22px; text-align: center;">
            <span style="font-size: 13px; font-weight: 800; color: #8A5A00;">
                🔥 Streak Aktifmu: {{ $student->current_streak }} Hari
            </span>
        </div>

        <div style="text-align: center; margin-bottom: 24px;">
            <a href="{{ url('/siswa/jurnal') }}"
               style="display: inline-block; background-color: #38BDF8; color: #0F2A43; padding: 12px 28px; border-radius: 14px; font-weight: 800; text-decoration: none; font-size: 14px; box-shadow: 0 4px 0 #0284C7;">
                Isi Jurnal Sekarang ✍️
            </a>
        </div>

        <p style="font-size: 11px; color: #6B829A; text-align: center; margin: 0; border-top: 1px solid #EAF1F8; padding-top: 16px;">
            Pesan otomatis dikirim oleh Sistem SiHebat SMAN 1 Sungailiat.<br>
            Batas pengisian jurnal harian adalah pukul 23.59 WIB.
        </p>
    </div>
</body>
</html>
