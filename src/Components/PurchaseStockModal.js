import React, { useState } from "react";
import { Box, Button, Grid, Modal, TextField, Typography } from "@mui/material";
import Autocomplete from "@mui/material/Autocomplete";

import CloseIcon from "@mui/icons-material/Close";

const PurchaseStockModal = ({
  open,
  onClose,
  allStocks,
  callBackPurchaseStock,
}) => {
  const [stockId, setStockId] = useState(null);
  const [stockName, setStockName] = useState("");
  const [volume, setVolume] = useState();

  const handleClick = () => {
    if (Number(volume) && Number(volume) > 0 && !stockId) {
      alert("Please select some stock");
    } else if (
      (!Number(volume) || Number(volume) == 0 || Number(volume) < 0) &&
      stockId
    ) {
      alert(
        "Please add volume to purchase & volume should be greater than zero"
      );
    } else if (Number(volume) && Number(volume) > 0 && stockId) {
      callBackPurchaseStock({ volume, stockId });
      onClose();
    } else {
      alert("Fill the missing fields");
    }
  };

  const handleStock = (value) => {
    const selected_stock = allStocks.filter((stock) => {
      return stock.stock_name == value;
    });
    if (selected_stock?.length) {
      setStockName(selected_stock[0]?.stock_name);
      setStockId(selected_stock[0]?.id);
    }
  };
  return (
    <Modal open={open} onClose={onClose}>
      <div className="purchase_stock_modal">
        <Grid container>
          <Grid item xs={12}>
            <Box className="purchase_modal_header">
              <Box>
                <Typography>Purchase a Stock</Typography>
              </Box>
              <Box>
                <CloseIcon
                  onClick={() => onClose()}
                  style={{ cursor: "pointer" }}
                />
              </Box>
            </Box>
          </Grid>
          <Grid item xs={12}>
            <Autocomplete
              id="combo-box-demo"
              value={stockName}
              onChange={(event, newValue) => {
                handleStock(newValue);
              }}
              options={allStocks.map((stock) => {
                return stock?.stock_name;
              })}
              style={{ marginTop: "1em" }}
              renderInput={(params) => (
                <TextField
                  required
                  fullWidth
                  {...params}
                  label={"Select Stock"}
                  title="Select Stock"
                />
              )}
            />
          </Grid>

          <Grid item xs={12}>
            <TextField
              fullWidth
              style={{ marginTop: "1em" }}
              onChange={(e) => setVolume(e?.target?.value)}
              value={volume}
              type="number"
              label="Add Volume"
              required
            />
          </Grid>
          <Grid item xs={12}>
            <Box className="purchase_btn_wrapper">
              <Button className="purchase_btn" onClick={handleClick}>
                {" "}
                Purcahse{" "}
              </Button>
            </Box>
          </Grid>
        </Grid>
      </div>
    </Modal>
  );
};

export default PurchaseStockModal;
