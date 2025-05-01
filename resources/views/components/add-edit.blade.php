<div class="modal fade" id="addPaymentMethodModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentMethodModalLabel">Add Payment Method</h5>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentMethodForm" method="POST">
                @csrf
                <input type="hidden" id="paymentMethodType" name="type">
                <input type="hidden" id="methodId" name="id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="label" class="form-label">Label (Optional)</label>
                        <input type="text" class="form-control" id="label" name="label" placeholder="e.g. Main Bank, BTC Wallet">
                    </div>
                    
                    <!-- Bank Fields -->
                    <div class="bank-fields d-none">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name">
                        </div>
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="account_name" name="account_name">
                        </div>
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number">
                        </div>
                        <div class="mb-3">
                            <label for="routing_number" class="form-label">Routing Number (Optional)</label>
                            <input type="text" class="form-control" id="routing_number" name="routing_number">
                        </div>
                        <div class="mb-3">
                            <label for="bank_address" class="form-label">Bank Address (Optional)</label>
                            <input type="text" class="form-control" id="bank_address" name="bank_address">
                        </div>
                        <div class="mb-3">
                            <label for="bank_reference" class="form-label">Reference (Optional)</label>
                            <input type="text" class="form-control" id="bank_reference" name="bank_reference">
                        </div>
                    </div>
                    
                    <!-- Crypto Fields -->
                    <div class="crypto-fields d-none">
                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="BTC">BTC</option>
                                <option value="ETH">ETH</option>
                                <option value="USDC (ERC20)">USDT (ERC20)</option>
                                <option value="USDT (TRC20)">USDT (TRC20)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="wallet_address" class="form-label">Wallet Address</label>
                            <input type="text" class="form-control" id="wallet_address" name="wallet_address">
                        </div>
                    </div>
                    
                    <!-- <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_withdrawal" name="is_withdrawal">
                        <label class="form-check-label" for="is_withdrawal">Use for withdrawals</label>
                        <small class="form-text text-muted d-block">Note: Only admin can set withdrawal methods</small>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>