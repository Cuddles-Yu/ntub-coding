<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog form-dialog modal-dialog-centered logout-modal" style="justify-content:center;">
    <div class="modal-content" style="width:auto">
      <div class="modal-body logout-modal-body">
        <form novalidate style="margin-top:15px;margin-left:20px;margin-right:20px;">
          <h2>會員登出</h2>
          <p>您會遺失所有未保存的資料，系統會自動跳轉網頁</p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-solid-gray" id="logout-cancel-button" data-bs-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="logout-confirm-button" onclick="logoutRequest()">登出</button>
      </div>
    </div>
  </div>
</div>

<script src="/scripts/logout.js" defer></script>