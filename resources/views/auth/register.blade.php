<!DOCTYPE html>
<html lang="en">

@include('layouts.head')

<body>

    <main>
        <div class="container">

            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <img src="assets/img/logo.png" style="width: 40%;">
                            <div class="d-flex justify-content-center py-4">
                            </div><!-- End Logo -->

                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                                        <p class="text-center small">Enter your personal details to create account</p>
                                    </div>

                                    <form class="row g-3 needs-validation" method="POST"
                                        action="{{ route('register') }}">
                                        @csrf
                                        <div class="col-12">
                                            <label for="yourName" class="form-label">Your Name</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <input type="text" name="name" class="form-control" id="yourName"
                                                    value="{{ old('name') }}" required>
                                                <div class="invalid-feedback">Please, enter your name!</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourEmail" class="form-label">Your Email</label>
                                            <div class="input-group has-validation">

                                                <input type="email" name="email" class="form-control" id="yourEmail"
                                                    value="{{ old('email') }}" required>
                                                <div class="invalid-feedback">Please enter a valid Email adddress!</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control"
                                                id="yourPassword" required>
                                            <div class="invalid-feedback">Please enter your password!</div>
                                        </div>

                                        @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        <div class="col-12">
                                            <legend class="col-form-label col-sm-2 pt-0">Role</legend>
                                            <div class="col-sm-10">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="role"
                                                        id="gridRadios1" value="user" checked>
                                                    <label class="form-check-label" for="role">
                                                        Customer
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="role"
                                                        id="gridRadios2" value="supplier">
                                                    <label class="form-check-label" for="role">
                                                        Supplier
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="role"
                                                        id="gridRadios2" value="retailer">
                                                    <label class="form-check-label" for="role">
                                                        Retailer
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" name="terms" type="checkbox" value=""
                                                    id="acceptTerms" required>
                                                <label class="form-check-label" for="acceptTerms">I agree and accept the
                                                    <a href="" data-bs-toggle="modal"
                                                        data-bs-target="#scrollingModal">terms
                                                        and
                                                        conditions</a></label>
                                                <div class="modal fade" id="scrollingModal" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Terms and Conditions</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" style="min-height: 1500px;">
                                                                <h5>Terms and Conditions of Use for G-23's
                                                                    Chocolate Supply Chain Management System</h5><br>
                                                                <b>1. Acceptance of Terms</b><br>

                                                                By registering for, accessing, or using the G-23's
                                                                Chocolate Supply Chain Management System
                                                                (the "System"), you ("User" or "you") agree to be bound
                                                                by these Terms and Conditions of Use (the "Terms"). If
                                                                you do not agree to these Terms, you are not authorized
                                                                to use the System. These Terms constitute a legally
                                                                binding agreement between you and G-23
                                                                ("we," "us," or "our").<br><br>

                                                                <b>2. Description of the System</b><br>

                                                                The System is a web-based platform designed to
                                                                facilitate the management of the chocolate supply chain,
                                                                from raw cocoa bean sourcing to finished product
                                                                distribution. The System provides features including,
                                                                but not limited to:<br>

                                                                <ul>
                                                                    <li>Inventory Tracking: Monitoring and management of
                                                                        cocoa beans, intermediate products, and finished
                                                                        chocolate inventory levels across the supply
                                                                        chain.</li>
                                                                    <li>Order Management: Placement, tracking, and
                                                                        fulfillment
                                                                        of orders for cocoa beans, semi-finished and
                                                                        finished
                                                                        products.</li>
                                                                    <li>Communication Platform: A communication tool
                                                                        enabling
                                                                        real-time communication and collaboration
                                                                        between Users
                                                                        within the supply chain.</li>
                                                                    <li>Reporting & Analytics: Generation of reports and
                                                                        analytics related to inventory, orders, sales,
                                                                        and other
                                                                        relevant supply chain data.</li>
                                                                    <li>Demand Forecasting: Machine learning-driven
                                                                        predictions
                                                                        of future demand for chocolate products.</li>
                                                                    <li>Customer Segmentation: Analysis of customer
                                                                        buying
                                                                        behavior to segment customers for targeted
                                                                        service and
                                                                        offerings.</li>

                                                                </ul>
                                                                <br>

                                                                <b>3. User Registration and Account Security</b><br>

                                                                <ul>
                                                                    <li>Registration: To access certain features of the
                                                                        System,
                                                                        you must register and create an account. You
                                                                        agree to
                                                                        provide accurate, current, and complete
                                                                        information
                                                                        during the registration process and to update
                                                                        such
                                                                        information to keep it accurate, current, and
                                                                        complete.</li>

                                                                    <li>Eligibility: You represent and warrant that you
                                                                        are at
                                                                        least 18 years of age and have the legal
                                                                        capacity to
                                                                        enter into these Terms. If you are using the
                                                                        System on
                                                                        behalf of a company or other legal entity, you
                                                                        represent
                                                                        and warrant that you have the authority to bind
                                                                        that
                                                                        entity to these Terms.</li>

                                                                    <li>Account Security: You are responsible for
                                                                        maintaining
                                                                        the confidentiality of your account login
                                                                        information
                                                                        and for all activities that occur under your
                                                                        account.
                                                                        You agree to notify us immediately of any
                                                                        unauthorized
                                                                        access to or use of your account. We are not
                                                                        liable for
                                                                        any loss or damage arising from your failure to
                                                                        protect
                                                                        your account information.</li>

                                                                    <li>Account Usage: Each user account is designated
                                                                        for a
                                                                        single individual. Sharing or transferring
                                                                        accounts is
                                                                        strictly prohibited.</li>
                                                                </ul><br>

                                                                <b>4. User Conduct</b><br>

                                                                You agree to use the System only for lawful purposes and
                                                                in a manner that does not infringe the rights of others
                                                                or restrict or inhibit their use and enjoyment of the
                                                                System. You agree not to:<br>

                                                                <ul>
                                                                    <li>Upload, post, transmit, or otherwise make
                                                                        available any
                                                                        content that is unlawful, harmful, threatening,
                                                                        abusive,
                                                                        harassing, tortious, defamatory, vulgar,
                                                                        obscene,
                                                                        libelous, invasive of another's privacy,
                                                                        hateful, or
                                                                        racially, ethnically, or otherwise
                                                                        objectionable.</li>

                                                                    <li>Impersonate any person or entity or falsely
                                                                        state or
                                                                        otherwise misrepresent your affiliation with a
                                                                        person or
                                                                        entity.</li>

                                                                    <li>Forge headers or otherwise manipulate
                                                                        identifiers in
                                                                        order to disguise the origin of any content
                                                                        transmitted
                                                                        through the System.</li>

                                                                    <li>Upload, post, transmit, or otherwise make
                                                                        available any
                                                                        content that you do not have a right to make
                                                                        available
                                                                        under any law or contractual or fiduciary
                                                                        relationship
                                                                        (such as inside information, proprietary and
                                                                        confidential information learned or disclosed as
                                                                        part of
                                                                        employment relationships or under nondisclosure
                                                                        agreements).</li>

                                                                    <li>Upload, post, transmit, or otherwise make
                                                                        available any
                                                                        content that infringes any patent, trademark,
                                                                        trade
                                                                        secret, copyright, or other proprietary rights
                                                                        of any
                                                                        party.</li>

                                                                    <li>Upload, post, transmit, or otherwise make
                                                                        available any
                                                                        unsolicited or unauthorized advertising,
                                                                        promotional
                                                                        materials, "junk mail," "spam," "chain letters,"
                                                                        "pyramid schemes," or any other form of
                                                                        solicitation.</li>

                                                                    <li>Upload, post, transmit, or otherwise make
                                                                        available any
                                                                        material that contains software viruses or any
                                                                        other
                                                                        computer code, files, or programs designed to
                                                                        interrupt,
                                                                        destroy, or limit the functionality of any
                                                                        computer
                                                                        software or hardware or telecommunications
                                                                        equipment.</li>

                                                                    <li>Interfere with or disrupt the System or servers
                                                                        or
                                                                        networks connected to the System, or disobey any
                                                                        requirements, procedures, policies, or
                                                                        regulations of
                                                                        networks connected to the System.</li>

                                                                    <li>"Stalk" or otherwise harass another user.</li>

                                                                    <li>Collect or store personal data about other users
                                                                        without
                                                                        their express consent.</li>

                                                                    <li>Attempt to gain unauthorized access to any
                                                                        portion of
                                                                        the System or any other accounts, computer
                                                                        systems, or
                                                                        networks connected to the System, through
                                                                        hacking,
                                                                        password mining, or any other means.</li>

                                                                    <li>Use any robot, spider, scraper, or other
                                                                        automated means
                                                                        to access the System for any purpose without our
                                                                        express
                                                                        written permission.</li>
                                                                </ul><br>

                                                                <b>5. Data Privacy and Security</b><br>

                                                                We are committed to protecting the privacy of your
                                                                data.
                                                                Our Privacy Policy, which is incorporated by
                                                                reference
                                                                into these Terms, explains how we collect, use, and
                                                                disclose your information. You can review our
                                                                Privacy
                                                                Policy later.<br><br>

                                                                We will implement and maintain reasonable security
                                                                measures to protect your data from unauthorized
                                                                access,
                                                                use, or disclosure. However, no method of
                                                                transmission
                                                                over the Internet or method of electronic storage is
                                                                completely secure. Therefore, we cannot guarantee
                                                                the
                                                                absolute security of your data.<br><br>

                                                                You are responsible for the security of your own
                                                                systems
                                                                and devices used to access the System.<br><br>

                                                                <b>6. Intellectual Property</b><br>

                                                                The System and its content, including but not
                                                                limited to
                                                                text, graphics, logos, images, software, and the
                                                                design,
                                                                selection, and arrangement thereof, are owned by
                                                                G-23 or
                                                                its licensors and are protected by
                                                                copyright, trademark, and other intellectual
                                                                property
                                                                laws.<br><br>

                                                                You are granted a limited, non-exclusive,
                                                                non-transferable license to access and use the
                                                                System
                                                                for your internal business purposes in accordance
                                                                with
                                                                these Terms.<br><br>

                                                                You may not modify, copy, distribute, transmit,
                                                                display,
                                                                perform, reproduce, publish, license, create
                                                                derivative
                                                                works from, transfer, or sell any information,
                                                                software,
                                                                products, or services obtained from the System,
                                                                except
                                                                as expressly permitted by these Terms or with our
                                                                prior
                                                                written consent.<br><br>

                                                                You retain ownership of the data you upload or input
                                                                into the System ("User Data"). However, you grant us
                                                                a
                                                                non-exclusive, worldwide, royalty-free license to
                                                                use,
                                                                reproduce, modify, adapt, publish, translate,
                                                                distribute, and display User Data for the purpose of
                                                                providing and improving the System. This includes
                                                                using
                                                                the data for machine learning and demand forecasting
                                                                algorithms.<br><br>

                                                                <b>7. Machine Learning and Data Usage</b><br>

                                                                You acknowledge and agree that we may use User Data,
                                                                including transaction data, order history, and
                                                                customer
                                                                information, to train and improve our machine
                                                                learning
                                                                algorithms for demand forecasting and customer
                                                                segmentation.<br><br>

                                                                We will take reasonable measures to anonymize and
                                                                aggregate User Data where possible when using it for
                                                                machine learning purposes. However, you understand
                                                                that
                                                                some User Data may be identifiable.<br><br>

                                                                The demand forecasts and customer segmentations
                                                                generated by the System are provided for
                                                                informational
                                                                purposes only and should not be relied upon as the
                                                                sole
                                                                basis for making business decisions. We make no
                                                                guarantees as to the accuracy or reliability of
                                                                these
                                                                predictions.<br><br>

                                                                <b>8. Third-Party Links and Services</b><br>

                                                                The System may contain links to third-party websites
                                                                or
                                                                services that are not owned or controlled by us. We
                                                                have
                                                                no control over, and assume no responsibility for,
                                                                the
                                                                content, privacy policies, or practices of any
                                                                third-party websites or services. You acknowledge
                                                                and
                                                                agree that we are not responsible or liable,
                                                                directly or
                                                                indirectly, for any damage or loss caused or alleged
                                                                to
                                                                be caused by or in connection with the use of or
                                                                reliance on any such content, goods, or services
                                                                available on or through any such websites or
                                                                services.<br><br>

                                                                <b>9. Fees and Payment</b><br>

                                                                You agree to pay all fees and charges associated
                                                                with
                                                                your use of the System in accordance with our
                                                                pricing
                                                                schedule.<br><br>

                                                                We reserve the right to change our fees and pricing
                                                                structure at any time. We will provide you with
                                                                reasonable notice of any such changes.<br><br>

                                                                Late payments may be subject to interest
                                                                charges.<br><br>

                                                                <b>10. Disclaimer of Warranties</b><br>

                                                                THE SYSTEM IS PROVIDED "AS IS" AND "AS AVAILABLE"
                                                                WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
                                                                INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
                                                                MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE,
                                                                AND
                                                                NON-INFRINGEMENT. WE DO NOT WARRANT THAT THE SYSTEM
                                                                WILL
                                                                BE UNINTERRUPTED, ERROR-FREE, OR FREE OF VIRUSES OR
                                                                OTHER HARMFUL COMPONENTS. WE DO NOT WARRANT THE
                                                                ACCURACY, COMPLETENESS, OR RELIABILITY OF ANY
                                                                INFORMATION OBTAINED THROUGH THE SYSTEM. YOU ARE
                                                                SOLELY
                                                                RESPONSIBLE FOR ANY DECISIONS YOU MAKE BASED ON
                                                                INFORMATION PROVIDED BY THE SYSTEM.<br><br>

                                                                <b>11. Limitation of Liability</b><br>

                                                                TO THE FULLEST EXTENT PERMITTED BY APPLICABLE LAW,
                                                                IN NO
                                                                EVENT SHALL G-23 BE LIABLE FOR ANY
                                                                INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR
                                                                PUNITIVE DAMAGES (INCLUDING, WITHOUT LIMITATION,
                                                                DAMAGES
                                                                FOR LOSS OF PROFITS, GOODWILL, USE, DATA, OR OTHER
                                                                INTANGIBLE LOSSES) ARISING OUT OF OR RELATING TO
                                                                YOUR
                                                                USE OF OR INABILITY TO USE THE SYSTEM, WHETHER BASED
                                                                ON
                                                                WARRANTY, CONTRACT, TORT (INCLUDING NEGLIGENCE), OR
                                                                ANY
                                                                OTHER LEGAL THEORY, EVEN IF WE HAVE BEEN ADVISED OF
                                                                THE
                                                                POSSIBILITY OF SUCH DAMAGES. OUR TOTAL LIABILITY TO
                                                                YOU
                                                                FOR ANY AND ALL CLAIMS ARISING OUT OF OR RELATING TO
                                                                THESE TERMS OR YOUR USE OF THE SYSTEM SHALL NOT
                                                                EXCEED
                                                                THE AMOUNT YOU PAID TO US FOR ACCESS TO THE SYSTEM
                                                                IN
                                                                THE MONTH IMMEDIATELY
                                                                PRECEDING THE EVENT GIVING RISE TO THE
                                                                CLAIM.<br><br>

                                                                <b>12. Indemnification</b><br>

                                                                You agree to indemnify, defend, and hold harmless
                                                                G-23
                                                                and its members from and against any
                                                                and all claims, liabilities, damages, losses, costs,
                                                                expenses, or fees (including reasonable attorneys'
                                                                fees)
                                                                arising out of or relating to your use of the
                                                                System,
                                                                your violation of these Terms, or your violation of
                                                                any
                                                                rights of another.<br><br>

                                                                <b>13. Termination</b><br>

                                                                We may terminate your access to the System at any
                                                                time,
                                                                with or without cause, and with or without notice.

                                                                You may terminate your account at any time by
                                                                deleting
                                                                your account within the application.

                                                                Upon termination, your right to access and use the
                                                                System will immediately cease. We may delete your
                                                                User
                                                                Data following a reasonable period after
                                                                termination, as
                                                                specified in our Privacy Policy.<br><br>

                                                                <b>14. Governing Law</b><br>

                                                                These Terms shall be governed by and construed in
                                                                accordance with the laws of Uganda,
                                                                without regard to its conflict of law
                                                                principles.<br><br>

                                                                <b>15. Dispute Resolution</b><br>

                                                                Any dispute arising out of or relating to these
                                                                Terms
                                                                shall be resolved through binding arbitration in
                                                                accordance with
                                                                the rules of Ugandan law. The venue for
                                                                any arbitration or legal proceeding shall be in
                                                                Kampala,
                                                                Uganda.<br><br>

                                                                <b>16. Changes to These Terms</b><br>

                                                                We may update these Terms from time to time. We will
                                                                post any changes on this page and will indicate the
                                                                "Last Updated" date at the top of the Terms. Your
                                                                continued use of the System after any such changes
                                                                constitutes your acceptance of the new
                                                                Terms.<br><br>

                                                                <b>17. Entire Agreement</b><br>

                                                                These Terms constitute the entire agreement between
                                                                you
                                                                and G-23 relating to the System and
                                                                supersede all prior or contemporaneous
                                                                communications
                                                                and proposals, whether oral or written, between you
                                                                and
                                                                us with respect to the System.<br><br>

                                                                <b>18. Severability</b><br>

                                                                If any provision of these Terms is held to be
                                                                invalid or
                                                                unenforceable, such provision shall be struck and
                                                                the
                                                                remaining provisions shall be enforced.<br><br>

                                                                <b>19. Waiver</b><br>

                                                                Our failure to enforce any right or provision of
                                                                these
                                                                Terms shall not constitute a waiver of such right or
                                                                provision unless acknowledged and agreed to by us in
                                                                writing.
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="invalid-feedback">You must agree before submitting.</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Create Account</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">Already have an account? <a
                                                    href="{{ route('login') }}">Log in</a></p>
                                        </div>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    @include('layouts.scripts')

</body>

</html>