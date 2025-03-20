@extends('layouts.app')

@section('content')
<style>
    .transaction-list {
    margin-top: 20px;
}

.transaction-item {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
}

.transaction-item:hover {
    transform: translateY(-5px);
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
}

.transaction-info {
    margin-bottom: 10px;
}

.transaction-date {
    font-size: 0.9rem;
    color: #6c757d;
}

.transaction-description {
    font-weight: bold;
    font-size: 1rem;
}

.transaction-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.transaction-amount {
    font-size: 1.2rem;
    color: #28a745;
}

.transaction-status {
    padding: 5px 10px;
    border-radius: 4px;
    text-align: center;
}

.status-pending {
    background-color: #ffc107;
    color: #fff;
}

.status-success {
    background-color: #28a745;
    color: #fff;
}

.status-failure {
    background-color: #dc3545;
    color: #fff;
}

</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header text-center font-weight-bold" style="background-color: #4CAF50; color: white;">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="font-weight-bold" style="font-size: 1.25rem;">{{ __('Welcome, ') . $userName . '!' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white text-center font-weight-bold">{{ __('Account Details') }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Saldo: ') }}</strong> <span style="font-size: 1.2rem; color: #4CAF50;">{{ $saldo }}</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{ __('No. Rekening: ') }}</strong> <span style="font-size: 1.2rem;">{{ $user_id }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white text-center font-weight-bold">{{ __('Status Credit') }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ __('Sisa Utang: ') }}</strong> <span style="font-size: 1.2rem;">{{ $sisaUtang }}</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-primary">Bayar Tagihan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    @if ($transactions->isEmpty())
                        <p class="no-transaction">Tidak ada transaksi</p>
                    @else
                        <h3>Transaksi terakhir</h3>
                        <div class="transaction-list">
                            @foreach ($transactions as $transaction)
                                <div class="transaction-item" data-toggle="modal" data-target="#transactionModal" onclick="openTransactionModal({{ $transaction->id }})">
                                    <div class="transaction-info">
                                        <div class="transaction-date">{{ $transaction->tanggal_transaksi }}</div>
                                        <div class="transaction-description">{{ $transaction->deskripsi }}</div>
                                    </div>
                                    <div class="transaction-details">
                                        <div class="transaction-amount">
                                            Rp {{ number_format(floatval($transaction->jumlah_transaksi), 0, '', '.') }}
                                        </div>
                                        <div class="transaction-status 
                                            @if ($transaction->status_hutang === 'belum_diterima' || $transaction->status_hutang === 'fee') 
                                                status-pending
                                            @elseif ($transaction->status_hutang === 'paid' || $transaction->status_hutang === 'voucher') 
                                                status-success
                                            @else 
                                                status-failure
                                            @endif">
                                            @if ($transaction->status_hutang === 'belum_diterima')
                                                Belum dibayar
                                            @elseif ($transaction->status_hutang === 'paid')
                                                Pengurangan
                                            @elseif ($transaction->status_hutang === 'voucher')
                                                Voucher
                                            @elseif ($transaction->status_hutang === 'tolak')
                                                Transaksi Ditolak
                                            @elseif ($transaction->status_hutang === 'fee')
                                                Biaya Layanan
                                            @else
                                                Paylater
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Transaction Details -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Transaksi Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Transaction details will be dynamically injected here -->
                <div id="transactionDetails">
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Open modal with transaction details
    function openTransactionModal(transactionId) {
        // You can load the transaction details dynamically, for example using AJAX
        // For now, we can simply display a placeholder
        const transactionDetails = {
            1: {
                tanggal_transaksi: '2025-03-18',
                deskripsi: 'Pembayaran Tagihan',
                jumlah_transaksi: 1500000,
                status_hutang: 'paid',
            },
            2: {
                tanggal_transaksi: '2025-03-17',
                deskripsi: 'Voucher Pengeluaran',
                jumlah_transaksi: 500000,
                status_hutang: 'voucher',
            },
        };

        const transaction = transactionDetails[transactionId] || null;
        if (transaction) {
            document.getElementById('transactionDetails').innerHTML = `
                <p><strong>Tanggal Transaksi:</strong> ${transaction.tanggal_transaksi}</p>
                <p><strong>Deskripsi:</strong> ${transaction.deskripsi}</p>
                <p><strong>Jumlah:</strong> Rp ${transaction.jumlah_transaksi.toLocaleString()}</p>
                <p><strong>Status:</strong> ${getTransactionStatus(transaction.status_hutang)}</p>
            `;
        }
    }

    function getTransactionStatus(status) {
        switch (status) {
            case 'paid': return 'Pembayaran Selesai';
            case 'voucher': return 'Voucher';
            case 'belum_diterima': return 'Belum Diterima';
            case 'fee': return 'Biaya Layanan';
            case 'tolak': return 'Ditolak';
            default: return 'Pending';
        }
    }
</script>
@endpush
