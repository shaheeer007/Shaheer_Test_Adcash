import React from "react";
import { Modal, Typography } from "@mui/material";

const ActiveUserModal = ({ open, onClose, allClients, selectedUserId }) => {
  const handleClick = (id) => {
    selectedUserId(id);
    onClose();
  };
  return (
    <Modal open={open} onClose={onClose}>
      <div className="user_modal">
        {allClients?.length &&
          allClients.map((client) => {
            return (
              <Typography
                className="user_text"
                onClick={() => handleClick(client?.id)}
              >
                {client?.name}
              </Typography>
            );
          })}
      </div>
    </Modal>
  );
};

export default ActiveUserModal;
