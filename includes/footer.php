</main>
<footer class="footer">
    <div class="container footer-content">
        <div class="footer-col footer-brand">
            <img src="../assets/img/logo-jti.png" alt="Logo JTI" class="h-8 w-auto"> 
            <p>Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141</p>
        </div>
        <div class="footer-col">
            <h4>Menu</h4>
            <ul>
                <li><a href="#">Beranda</a></li>
                <li><a href="#">Profil</a></li>
                <li><a href="#">Arsip</a></li>
                <li><a href="#">Galeri</a></li>
                <li><a href="#">Layanan</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Kontak Kami</h4>
            <p>+628123456789</p>
            <p>Email: labncs@mail.com</p>
        </div>
        <div class="footer-col">
            <h4>Social</h4>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <div class="copyright-bar text-center">
        &copy; 2025 Laboratorium Network & Security | All Rights Reserved
    </div>
</footer>

<style>
    /* Variabel yang dibutuhkan footer */
    :root {
        --primary-color: #004d99;
        /* Biru Tua */
        --secondary-color: #ff6600;
        /* Oranye */
    }

    /* --- Footer Styles --- */
    .footer {
        background-color: var(--primary-color);
        color: white;
        padding: 30px 0 0;
        font-family: Arial, sans-serif;
        /* Pastikan font konsisten */
    }

    .footer .container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 30px;
        gap: 30px;
    }

    .footer-col {
        max-width: 250px;
    }

    .footer-logo {
        width: 40px;
        height: 40px;
        margin-bottom: 15px;
        background-color: white;
        /* Placeholder */
        border-radius: 4px;
    }

    .footer-col h4 {
        margin-bottom: 15px;
        font-size: 1.1em;
        color: white;
    }

    .footer-col p {
        font-size: 0.9em;
        margin-bottom: 10px;
    }

    .footer-col ul {
        list-style: none;
        padding: 0;
    }

    .footer-col ul li a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        display: block;
        margin-bottom: 8px;
        font-size: 0.9em;
        transition: color 0.2s;
    }

    .footer-col ul li a:hover {
        color: var(--secondary-color);
    }

    .social-links a {
        color: white;
        margin-right: 15px;
        font-size: 1.2em;
        transition: color 0.2s;
    }

    .social-links a:hover {
        color: var(--secondary-color);
    }

    .copyright-bar {
        padding: 20px 0;
        font-size: 0.9em;
        color: rgba(255, 255, 255, 0.6);
        text-align: center;
    }

    /* --- Responsiveness Footer Saja --- */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }

        .footer-col {
            margin-bottom: 20px;
            max-width: 90%;
        }
    }
</style>

</body>

</html>