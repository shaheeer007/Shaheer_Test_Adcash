import React, { useState } from "react";
import { Grid, Typography, Box, Button } from "@mui/material";
import "./LandingPage.css";
import { DataGrid } from "@mui/x-data-grid";
import ArrowDropDownOutlinedIcon from "@mui/icons-material/ArrowDropDownOutlined";
import ActiveUserModal from "../../Components/ActiveUserModal";
import PurchaseStockModal from "../../Components/PurchaseStockModal";
function LandingPagePresenter({
  columns,
  rows,
  handlePurchaseStock,
  handleSelectedNewUserId,
  profitableClients,
  walletInfoData,
  allClients,
  allStocks,
  activeUser,
}) {
  const [userModal, setUserModal] = useState(false);
  const [purchaseModal, setPurchaseModal] = useState(false);
  console.log('--->>',allStocks)
  return (
    <>
      <Grid container>
        <Grid item xs={12}>
          <Box className="lp_heading">
            <Box>
              <Typography className="left_side_heading">Logo</Typography>
            </Box>
            <Box>
              <Typography className="active_user">
                {activeUser?.name}{" "}
                <ArrowDropDownOutlinedIcon
                  onClick={() => setUserModal(true)}
                  style={{ cursor: "pointer" }}
                />
              </Typography>{" "}
            </Box>
          </Box>
        </Grid>
      </Grid>
      <Grid container>
        <Grid item xs={9}>
          <Box className="detail_left">
            <Box>
              <Typography>General Information</Typography>
            </Box>
            <Box className="info_boxes_wrapper">
              <Box className="info_box">
                <Typography>Current Balance</Typography>
                <Typography className="box_data_val">
                  € {walletInfoData?.current_amount}
                </Typography>
              </Box>
              <Box className="info_box">
                <Typography>Total Profit/Loss</Typography>
                <Typography className="box_data_val">
                  {Number(walletInfoData?.total_loss_or_gain) == 0
                    ? " "
                    : Number(walletInfoData?.total_loss_or_gain) > 0
                    ? "+ "
                    : Number(walletInfoData?.total_loss_or_gain) < 0
                    ? "- "
                    : ""}
                  € {Math.abs(walletInfoData?.total_loss_or_gain)}
                </Typography>
              </Box>
              <Box className="info_box">
                <Typography>Total Portfolio Value</Typography>
                <Typography className="box_data_val">
                  {Number(walletInfoData?.portfolio_price)}
                </Typography>
              </Box>
            </Box>
            <Box className="secnd_header">
              <Box>
                <Typography>Transactions</Typography>
              </Box>
              <Button
                className="create_btn"
                onClick={() => setPurchaseModal(true)}
              >
                Create New Purchase
              </Button>
            </Box>
            <Box className="main_table_wrapper">
              <Box className="main_table">
                <DataGrid
                  columns={columns}
                  rows={rows}
                  checkboxSelection={false}
                />
              </Box>
            </Box>
          </Box>
        </Grid>
        <Grid item xs={3}>
          <Box className="detail_right">
            <Box>
              <Typography className="left_side_heading">
                Recent Stocks
              </Typography>
            </Box>
            {allStocks && allStocks?.length > 0 ? (
              allStocks.map((stock) => {
                return (
                  <Box className="stock_box">
                    <Typography>{stock?.stock_name}</Typography>
                    <Typography>{`€ ${stock?.current_price}`}</Typography>
                  </Box>
                );
              })
            ) : (
              <div className="no_data">Currently, Not any Stock</div>
            )}
            <Box style={{ marginTop: "4em" }}>
              <Typography className="left_side_heading">
                Most Profitable Clients
              </Typography>
            </Box>
            {profitableClients && profitableClients?.length > 0 ? (
              profitableClients.map((data) => {
                if (data?.total && data?.total > 0) {
                  return (
                    <Box className="stock_box">
                      <Typography
                        className="proftitable_client_name"
                        onClick={() => {
                          handleSelectedNewUserId(data?.id);
                        }}
                      >
                        {data?.name}
                      </Typography>
                      <Typography>{`€ ${data?.total}`}</Typography>
                    </Box>
                  );
                }
              })
            ) : (
              <div className="no_data">Currently, Not any Client</div>
            )}
          </Box>
        </Grid>
      </Grid>
      {userModal && (
        <ActiveUserModal
          onClose={() => setUserModal(false)}
          open={userModal}
          allClients={allClients}
          selectedUserId={(id) => handleSelectedNewUserId(id)}
        />
      )}
      {purchaseModal && (
        <PurchaseStockModal
          onClose={() => setPurchaseModal(false)}
          open={purchaseModal}
          allStocks={allStocks}
          callBackPurchaseStock={handlePurchaseStock}
        />
      )}
    </>
  );
}

export default LandingPagePresenter;
