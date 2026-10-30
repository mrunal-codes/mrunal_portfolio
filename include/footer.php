<?php
// include/footer.php - Simple Footer with Fixed Floating Icons
?>
    <hr>

    <!-- ============================================
         SIMPLE FOOTER
         ============================================ -->
    <footer class="footer">
        <h3 class="footer-name">Mrunal Hyalis</h3>
        <p class="footer-role">Web Developer | MIS Executive</p>
        <p class="footer-copy" style="margin-top: 15px;">
            © <?= date('Y') ?> Mrunal Hyalis. All Rights Reserved.
        </p>

        <!-- ============================================
             FLOATING SOCIAL ICONS (Yahan footer ke andar hai)
             ============================================ -->
        <div class="floating-socials">
            <!-- WhatsApp -->
            <a href="https://wa.me/917038501758" target="_blank" class="social-icon whatsapp" title="WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
            
            <!-- GitHub -->
            <a href="https://github.com/mrunal1234-cmd" target="_blank" class="social-icon github" title="GitHub">
                <i class="fa-brands fa-github"></i>
            </a>
            
            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/in/mrunal-hyalis-54406b3a4/" target="_blank" class="social-icon linkedin" title="LinkedIn">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
        </div>
    </footer>

    <!-- ============================================
         STYLES
         ============================================ -->
    <style>
        /* Footer Styles */
        .footer {
            text-align: center;
            padding: 1.5rem 1rem 1.2rem 1rem;
            margin-top: 1.5rem;
            position: relative; /* Icons ko footer ke andar position karne ke liye */
        }

        .footer-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.15rem 0;
            letter-spacing: 0.3px;
        }

        .footer-role {
            font-size: 0.78rem;
            color: #64748b;
            margin: 0 0 0.7rem 0;
            letter-spacing: 0.3px;
        }

        .footer-copy {
            font-size: 0.8rem;
            color: #475569;
            margin: 0;
            letter-spacing: 0.2px;
        }

        /* ============================================
           FLOATING SOCIAL ICONS STYLES (Fixed Position)
           ============================================ */
        .floating-socials {
            position: fixed !important; /* Scroll karne par bhi screen par fix rahega */
            right: 25px !important;     
            bottom: 30px !important;    
            display: flex;
            flex-direction: column; 
            gap: 15px;       
            z-index: 99999 !important; /* Sabse upar dikhne ke liye */
        }

        .floating-socials .social-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.3rem;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 2px solid #ffffff; 
        }

        /* Colors & Hover Effects */
        .floating-socials .social-icon.whatsapp { background-color: #25D366 !important; }
        .floating-socials .social-icon.whatsapp:hover { background-color: #1ebe58 !important; transform: scale(1.15); }

        .floating-socials .social-icon.github { background-color: #333333 !important; }
        .floating-socials .social-icon.github:hover { background-color: #000000 !important; transform: scale(1.15); }

        .floating-socials .social-icon.linkedin { background-color: #0077b5 !important; }
        .floating-socials .social-icon.linkedin:hover { background-color: #005582 !important; transform: scale(1.15); }

        /* Mobile Adjustment */
        @media (max-width: 768px) {
            .floating-socials {
                right: 15px !important;
                bottom: 20px !important; 
                gap: 10px;
            }
            .floating-socials .social-icon {
                width: 42px;
                height: 42px;
                font-size: 1.1rem;
            }
        }
    </style>

</div> <!-- /.portfolio-wrapper -->
</body>
</html>