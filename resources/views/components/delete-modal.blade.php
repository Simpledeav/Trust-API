<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">Delete Action</h5>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body my-4 py-4">
                <h5 class="">Are you sure you want delete this <span id="deleteModelName"></span>?</h5>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks
    document.querySelectorAll('[data-delete-button]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the form action and model name from data attributes
            const formAction = this.closest('form').action;
            const modelName = this.dataset.modelName || 'item';
            
            // Set the modal content
            document.getElementById('deleteModelName').textContent = modelName;
            document.getElementById('deleteForm').action = formAction;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            modal.show();
        });
    });
});
</script>