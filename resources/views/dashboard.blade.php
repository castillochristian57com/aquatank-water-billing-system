@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">AquaTank Dashboard</h1>
            <p class="text-muted mb-0">
                Water Billing System
            </p>
        </div>

        <div>
            <span class="badge text-bg-primary">
                Barangay Caruhayon
            </span>
        </div>
    </div>

@stop


@section('content')

    {{-- SUMMARY CARDS --}}
    <div class="row g-3">

        {{-- CONSUMERS --}}
        <div class="col-lg-3 col-md-6">

            <x-adminlte-small-box
                title="128"
                text="Total Consumers"
                icon="bi bi-people-fill"
                theme="primary"
                url="{{ url('/consumers') }}"
            />

        </div>


        {{-- BILLS --}}
        <div class="col-lg-3 col-md-6">

            <x-adminlte-small-box
                title="56"
                text="Pending Bills"
                icon="bi bi-receipt"
                theme="warning"
                url="#"
            />

        </div>


        {{-- PAYMENTS --}}
        <div class="col-lg-3 col-md-6">

            <x-adminlte-small-box
                title="₱18,450"
                text="Collected This Month"
                icon="bi bi-cash-stack"
                theme="success"
                url="#"
            />

        </div>


        {{-- SMS --}}
        <div class="col-lg-3 col-md-6">

            <x-adminlte-small-box
                title="42"
                text="SMS Notifications"
                icon="bi bi-chat-dots-fill"
                theme="info"
                url="#"
            />

        </div>

    </div>


    {{-- SECOND ROW --}}
    <div class="row g-3 mt-1">

        {{-- BILLING OVERVIEW --}}
        <div class="col-lg-8">

            <x-adminlte-card
                title="Billing Overview"
                icon="bi bi-bar-chart-fill"
                theme="primary"
                collapsible
            >

                <div class="row">

                    <div class="col-md-4 text-center">

                        <h2 class="text-primary">
                            ₱35,800
                        </h2>

                        <p class="text-muted">
                            Total Billing
                        </p>

                    </div>


                    <div class="col-md-4 text-center">

                        <h2 class="text-success">
                            ₱18,450
                        </h2>

                        <p class="text-muted">
                            Collected
                        </p>

                    </div>


                    <div class="col-md-4 text-center">

                        <h2 class="text-danger">
                            ₱17,350
                        </h2>

                        <p class="text-muted">
                            Outstanding
                        </p>

                    </div>

                </div>

                <hr>

                <div class="progress" style="height: 25px;">

                    <div
                        class="progress-bar bg-success"
                        role="progressbar"
                        style="width: 52%;"
                    >
                        52% Collected
                    </div>

                </div>

            </x-adminlte-card>

        </div>


        {{-- QUICK ACTIONS --}}
        <div class="col-lg-4">

            <x-adminlte-card
                title="Quick Actions"
                icon="bi bi-lightning-fill"
                theme="warning"
            >

                <div class="d-grid gap-2">

                    <a
                        href="#"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-person-plus"></i>
                        Add Consumer
                    </a>


                    <a
                        href="#"
                        class="btn btn-success"
                    >
                        <i class="bi bi-receipt"></i>
                        Create Bill
                    </a>


                    <a
                        href="#"
                        class="btn btn-info"
                    >
                        <i class="bi bi-chat-dots"></i>
                        Send SMS
                    </a>


                    <a
                        href="#"
                        class="btn btn-secondary"
                    >
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        Generate Report
                    </a>

                </div>

            </x-adminlte-card>

        </div>

    </div>


    {{-- THIRD ROW --}}
    <div class="row g-3">

        {{-- RECENT PAYMENTS --}}
        <div class="col-lg-7">

            <x-adminlte-card
                title="Recent Payments"
                icon="bi bi-credit-card"
                theme="success"
            >

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>Consumer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td>Juan Dela Cruz</td>

                                <td>
                                    ₱450.00
                                </td>

                                <td>
                                    Aug 25, 2026
                                </td>

                                <td>
                                    <span class="badge text-bg-success">
                                        Paid
                                    </span>
                                </td>

                            </tr>


                            <tr>

                                <td>Maria Santos</td>

                                <td>
                                    ₱620.00
                                </td>

                                <td>
                                    Aug 24, 2026
                                </td>

                                <td>
                                    <span class="badge text-bg-success">
                                        Paid
                                    </span>
                                </td>

                            </tr>


                            <tr>

                                <td>Pedro Garcia</td>

                                <td>
                                    ₱380.00
                                </td>

                                <td>
                                    Aug 23, 2026
                                </td>

                                <td>
                                    <span class="badge text-bg-success">
                                        Paid
                                    </span>
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </x-adminlte-card>

        </div>


        {{-- SYSTEM SUMMARY --}}
        <div class="col-lg-5">

            <x-adminlte-card
                title="System Summary"
                icon="bi bi-speedometer2"
                theme="info"
            >

                <div class="mb-3">

                    <div class="d-flex justify-content-between">

                        <span>
                            Consumers
                        </span>

                        <strong>
                            128
                        </strong>

                    </div>

                    <div class="progress mt-2">

                        <div
                            class="progress-bar bg-primary"
                            style="width: 80%;"
                        ></div>

                    </div>

                </div>


                <div class="mb-3">

                    <div class="d-flex justify-content-between">

                        <span>
                            Bills Paid
                        </span>

                        <strong>
                            72
                        </strong>

                    </div>

                    <div class="progress mt-2">

                        <div
                            class="progress-bar bg-success"
                            style="width: 72%;"
                        ></div>

                    </div>

                </div>


                <div class="mb-3">

                    <div class="d-flex justify-content-between">

                        <span>
                            Pending Bills
                        </span>

                        <strong>
                            56
                        </strong>

                    </div>

                    <div class="progress mt-2">

                        <div
                            class="progress-bar bg-warning"
                            style="width: 44%;"
                        ></div>

                    </div>

                </div>


                <div>

                    <div class="d-flex justify-content-between">

                        <span>
                            SMS Sent
                        </span>

                        <strong>
                            42
                        </strong>

                    </div>

                    <div class="progress mt-2">

                        <div
                            class="progress-bar bg-info"
                            style="width: 65%;"
                        ></div>

                    </div>

                </div>

            </x-adminlte-card>

        </div>

    </div>

@stop
