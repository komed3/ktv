            </main>
            <footer id="footer">
                <div class="footer-inner">
                    <nav role="footer">
                        <?php wp_nav_menu( [
                            'theme_location' => 'footer',
                            'container' => '',
                            'fallback_cb' => false
                        ] ); ?>
                    </nav>
                    <div class="credits">
                        <?php __credits( true ); ?>
                    </div>
                </div>
            </footer>
        </div>
        <?php wp_footer(); ?>
    </body>
</html>