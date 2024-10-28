import paxplot
import pandas as pd
import matplotlib.pyplot as plt

paxfig = paxplot.pax_parallel(n_axes=3)

df = pd.read_csv(r"D:\虛擬 Virtual\C槽\Desktop\score.csv")
cols = df.columns
paxfig.plot(
    df.to_numpy(),
    line_kwargs={'color': 'grey', 'alpha': 0.2, 'linewidth': 0.5},
)

data1 = [
    [538,57.4458,62.7553],
]
paxfig.plot(data1, line_kwargs={'color': 'red', 'linewidth': 1})

data2 = [
    [431,83.1410,80.5034],
]
paxfig.plot(data2, line_kwargs={'color': 'orange', 'linewidth': 1})

data3 = [
    [86,72.2222,74.5581],
]
paxfig.plot(data3, line_kwargs={'color': 'green', 'linewidth': 1})

data4 = [
    [30,90.1786,77.1836],
]
paxfig.plot(data4, line_kwargs={'color': 'blue', 'linewidth': 1})

data5 = [
    [26,25.0000,70.2249],
]
paxfig.plot(data5, line_kwargs={'color': 'purple', 'linewidth': 1})

# Add labels
paxfig.set_even_ticks(
    ax_idx=0,
    precision=0,
    maximum=600,
    minimum=0,
    n_ticks=2
)
paxfig.set_even_ticks(
    ax_idx=1,
    maximum=100,
    minimum=0,
    precision=0,
    n_ticks=2
)
paxfig.set_even_ticks(
    ax_idx=2,
    maximum=100,
    minimum=0,
    precision=0,
    n_ticks=2,
)
plt.show()