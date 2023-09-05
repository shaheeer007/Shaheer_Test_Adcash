import React, { useEffect, useState } from "react";
import LandingPagePresenter from "./LandingPagePresenter";
import axios from "axios";
import moment from "moment/moment";

function LandingPageController() {
  const [allStocks, setAllStocks] = useState([]);
  const [allClients, setAllClients] = useState([]);
  const [transactionsRecord, setTransactionsRecord] = useState([]);
  const [activeUser, setActiveUser] = useState({
    isActive: false,
    id: null,
    name: "",
  });
  const [walletInfoData, setWalletInfoData] = useState({
    current_amount: 0,
    total_loss_or_gain: 0,
    portfolio_price: 0,
  });
  const [profitableClients, setProfitableClients] = useState([]);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = () => {
    let all_transactions_data = [];
    axios
      .get("http://localhost/shaheer_test/my_backend/api/getAllStocks")
      .then((res) => {
        if(res && res?.data?.length > 0){
          setAllStocks(res?.data);
        } else {
          setAllStocks([]);
        }
     
      })
      .catch((err) => {
        console.log(err);
      });
    axios
      .get("http://localhost/shaheer_test/my_backend/api/getAllClients")
      .then((res) => {
        console.log('resss h',res)
       if(res && res?.data) {
        const active_user = res?.data?.filter((data) => {
          return data?.is_active == 1;
        });
        if (active_user?.length > 1) {
          alert("Error, multipl users cannot be active at same time");
        } else {
          if (active_user?.length == 1) {
            setActiveUser({
              isActive: true,
              id: active_user[0]?.id,
              name: active_user[0]?.name,
            });
            axios
              .get(
                `http://localhost/shaheer_test/my_backend/api/getAllTransactions?user_id=${active_user[0]?.id}`
              )
              .then((res) => {
                if (res?.data?.error) {
                  alert(res?.data?.error);
                } else {
                  all_transactions_data = res?.data;
                  setTransactionsRecord(res?.data);
                }
              })
              .catch((err) => console.log(err));
            axios
              .get(
                `http://localhost/shaheer_test/my_backend/api/getWalletInfo?user_id=${active_user[0]?.id}`
              )
              .then((res) => {
                //calculate total loss OR gain
                let gain_or_loss_amount = 0;
                res?.data?.gain_loss.map((data) => {
                  gain_or_loss_amount = gain_or_loss_amount + data?.gain_loss;
                });

                let totalPortfolioPrice = 0;
                for (const stock of all_transactions_data) {
                  const stockValue = stock.volume * stock.stock_current_price;
                  totalPortfolioPrice += stockValue;
                }
                setWalletInfoData({
                  current_amount: res?.data?.current_amount[0]?.amount,
                  total_loss_or_gain: gain_or_loss_amount,
                  portfolio_price: totalPortfolioPrice,
                });
              })
              .catch((err) => {
                console.log(err);
              });
          }
          const in_active_users = res?.data?.filter((data) => {
            return data?.is_active == 0;
          });
          if (in_active_users?.length > 0) {
            setAllClients(in_active_users);
          }
        } 
       }
      })
      .catch((err) => {
        console.log(err);
      });
    axios
      .get(
        `http://localhost/shaheer_test/my_backend/api/getAllTransactions?user_id=0`
      )
      .then((res) => {
        if (res?.data?.error) {
          alert(res?.data?.error);
        } else {
          const groupedData = {};

          // Iterate through the array and group by user_id
          res?.data.forEach((item) => {
            const { user_id, ...rest } = item;

            if (!groupedData[user_id]) {
              groupedData[user_id] = [];
            }

            groupedData[user_id].push(rest);
          });
          let final_array = [];
          Object.keys(groupedData).forEach((user_id) => {
            let total = 0;
            groupedData[user_id].map((item, index) => {
              total = total + item?.gain_loss;
              if (index == groupedData[user_id]?.length - 1) {
                final_array.push({
                  id: item?.client_id,
                  name: item?.client_name,
                  total: total,
                });
              }
            });
          });
          const sortedData = final_array.sort((a, b) => b.total - a.total);
          setProfitableClients(sortedData);
        }
      })
      .catch((err) => console.log(err));
  };

  const handleSelectedNewUserId = (id) => {
    const payload = {
      user_id: id,
    };
    axios
      .post(
        `http://localhost/shaheer_test/my_backend/api/getAllClients`,
        payload
      )
      .then((res) => {
        fetchData();
      })
      .catch((err) => {
        console.log(err);
      });
  };

  const handlePurchaseStock = (data) => {
    const payload = {
      user_id: activeUser?.id,
      volume: data?.volume,
      stockId: data?.stockId,
    };
    axios
      .post(
        `http://localhost/shaheer_test/my_backend/api/getAllTransactions`,
        payload
      )
      .then((res) => {
        if (res?.data?.error) {
          alert(res?.data?.error);
        } else {
          fetchData();
        }
      })
      .catch((err) => {
        console.log(err);
      });
  };

  const columns = [
    { field: "stock", headerName: "Stock", width: 200 },
    {
      field: "volume",
      headerName: "Volume",
      renderCell: (params) => {
        return <div style={{ fontWeight: "bold" }}>{params?.row?.volume}</div>;
      },
    },
    {
      field: "purchasePrice",
      headerName: "Purchase Price",
      renderCell: (params) => {
        return (
          <div style={{ fontWeight: "bold" }}>{params?.row?.purchasePrice}</div>
        );
      },
    },
    {
      field: "currentPrice",
      headerName: "Current Price",
      renderCell: (params) => {
        return (
          <div style={{ fontWeight: "bold" }}>{params?.row?.currentPrice}</div>
        );
      },
    },
    {
      field: "gainLoss",
      headerName: "Gain/Loss",
      renderCell: (params) => {
        const gainLossValue = Number(params?.row?.gainLoss);
        const symbol =
          params?.row?.is_loss == 1
            ? "-"
            : params?.row?.is_loss != 1 && params?.row?.is_gain != 1
            ? ""
            : "+";
        // Define custom styles based on gain/loss
        const cellStyle = {
          color:
            params?.row?.is_loss == 1
              ? "red"
              : params?.row?.is_loss != 1 && params?.row?.is_gain != 1
              ? "inherit"
              : "green",
          fontWeight: "bold",
        };

        return (
          <div style={cellStyle}>{`${symbol} € ${Math.abs(
            Number(gainLossValue)
          )}`}</div>
        );
      },
    },
    {
      field: "purchaseTime",
      headerName: "Purchase Time",
    },
  ];

  const rows =
    transactionsRecord && transactionsRecord?.length > 0
      ? transactionsRecord.map((data) => {
          return {
            id: data?.id,
            stock: data?.stock_name,
            volume: data?.volume,
            purchasePrice: `€ ${data?.purchase_price}`,
            currentPrice: `€ ${data?.stock_current_price}`,
            gainLoss: Number(data?.gain_loss),

            is_gain: data?.is_gain,
            is_loss: data?.is_loss,
            purchaseTime: moment(data?.purchase_time).format(
              "DD.MM.YYYY HH:MM"
            ),
          };
        })
      : [];
  return (
    <LandingPagePresenter
      columns={columns}
      rows={rows}
      handlePurchaseStock={(data) => {
        handlePurchaseStock(data);
      }}
      handleSelectedNewUserId={(id) => {
        handleSelectedNewUserId(id);
      }}
      profitableClients={profitableClients}
      walletInfoData={walletInfoData}
      allClients={allClients}
      allStocks={allStocks}
      activeUser={activeUser}
    />
  );
}

export default LandingPageController;
