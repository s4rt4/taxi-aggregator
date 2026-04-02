@extends('layouts.app')
@section('title', 'Contact Us')
@section('meta_description', 'Get in touch with our team. 24/7 customer support for passengers and operators.')

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, #1a2332 0%, #2d3e50 100%);">
    <div class="container text-center text-white">
        <h1 class="fw-bold mb-2">Contact Us</h1>
        <p class="lead opacity-75 mb-0">We're here to help 24 hours a day, 7 days a week</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            {{-- Contact Methods --}}
            <div class="col-lg-5">
                <h2 class="fw-bold mb-4">Get in Touch</h2>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:48px;height:48px;">
                            <i class="bi bi-telephone text-primary fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Phone</h6>
                            <p class="text-muted small mb-0">0800 123 4567 (Freephone, 24/7)</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width:48px;height:48px;">
                            <i class="bi bi-envelope text-success fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Email</h6>
                            <p class="text-muted small mb-0">
                                General: <a href="mailto:support@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.co.uk">support@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.co.uk</a><br>
                                Operators: <a href="mailto:operators@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.co.uk">operators@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.co.uk</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10" style="width:48px;height:48px;">
                            <i class="bi bi-chat-dots text-info fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Live Chat</h6>
                            <p class="text-muted small mb-0">Available on every page via the chat icon (bottom right)</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10" style="width:48px;height:48px;">
                            <i class="bi bi-whatsapp text-success fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">WhatsApp</h6>
                            <p class="text-muted small mb-0">Message us on +44 7000 000 000</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10" style="width:48px;height:48px;">
                            <i class="bi bi-geo-alt text-danger fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Office</h6>
                            <p class="text-muted small mb-0">{{ config('app.name') }} Ltd<br>United Kingdom</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Send Us a Message</h4>
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Your Name</label>
                                    <input type="text" class="form-control" placeholder="John Smith" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Email Address</label>
                                    <input type="email" class="form-control" placeholder="john@example.com" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Subject</label>
                                    <select class="form-select">
                                        <option>General Enquiry</option>
                                        <option>Booking Issue</option>
                                        <option>Operator Registration</option>
                                        <option>Payment / Refund</option>
                                        <option>Complaint</option>
                                        <option>Partnership</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Booking Reference (optional)</label>
                                    <input type="text" class="form-control" placeholder="TX-20260401-A1B2">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Message</label>
                                    <textarea class="form-control" rows="5" placeholder="How can we help?" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="fw-bold mb-3">Response Times</h3>
        <div class="row g-3 justify-content-center">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <div class="fw-bold text-primary fs-4">< 1 min</div>
                    <div class="small text-muted">Phone & Live Chat</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <div class="fw-bold text-primary fs-4">< 2 hrs</div>
                    <div class="small text-muted">Email (Urgent)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <div class="fw-bold text-primary fs-4">< 24 hrs</div>
                    <div class="small text-muted">Email (General)</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
